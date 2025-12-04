<?php

namespace App\Services\Import;

use App\Models\Order;
use App\Models\Patient;
use App\Models\TestResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ResultsImporter
{
    public function __construct(
        private CsvReader $reader,
        private CsvRowValidator $validator
    ) {}

    /**
     * Importuje plik CSV.
     *
     * @return array ['success' => int, 'errors' => int]
     */
    public function import(string $path, string $delimiter = ';'): array
    {
        $success = 0;
        $errors  = 0;

        $headerValidated = false;

        foreach ($this->reader->read($path, $delimiter) as $item) {
            if ($item['type'] === 'header') {
                // Walidacja nagłówka
                $this->validator->validateHeader($item['data']);
                $headerValidated = true;
                continue;
            }

            if (! $headerValidated) {
                throw new \RuntimeException('Nagłówek CSV nie został poprawnie odczytany.');
            }

            $row = $item['data'];

            // Walidacja wiersza
            $missing = $this->validator->validate($row);
            if ($missing !== []) {
                $errors++;

                Log::channel('results_import')->error('Brakujące pola w wierszu CSV', [
                    'data'    => $row,
                    'missing' => $missing,
                ]);

                continue;
            }

            try {
                DB::beginTransaction();

                $patient = $this->getOrCreatePatient($row);

                $order = $this->getOrCreateOrder($row, $patient);

                $this->createTestResult($row, $order);

                DB::commit();

                $success++;

                Log::channel('results_import')->info('Zaimportowano wiersz CSV', [
                    'patient_id_ext' => $row['patientId'],
                    'order_id_ext'   => $row['orderId'],
                    'test_name'      => $row['testName'],
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                $errors++;

                Log::channel('results_import')->error('Błąd podczas importu wiersza CSV', [
                    'data'  => $row,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::channel('results_import')->info('Import CSV zakończony', [
            'file'    => $path,
            'success' => $success,
            'errors'  => $errors,
        ]);

        return compact('success', 'errors');
    }

    /**
     * Tworzy / znajduje pacjenta na podstawie wiersza CSV.
     */
    private function getOrCreatePatient(array $row): Patient
    {
        return Patient::firstOrCreate(
            ['patient_id_ext' => $row['patientId']],
            [
                'name'       => $row['patientName'],
                'surname'    => $row['patientSurname'],
                'sex'        => strtolower($row['patientSex']),
                'birth_date' => $row['patientBirthDate'],
                'login'      => $this->buildLogin($row['patientName'], $row['patientSurname']),
                'password'   => Hash::make($row['patientBirthDate']),
            ]
        );
    }

    /**
     * Tworzy / znajduje zlecenie na podstawie wiersza CSV.
     */
    private function getOrCreateOrder(array $row, Patient $patient): Order
    {
        return Order::firstOrCreate(
            ['order_id_ext' => $row['orderId']],
            ['patient_id' => $patient->id]
        );
    }

    /**
     * Tworzy wynik badania.
     */
    private function createTestResult(array $row, Order $order): TestResult
    {
        return TestResult::create([
            'order_id'  => $order->id,
            'name'      => $row['testName'],
            'value'     => $row['testValue'],
            'reference' => $row['testReference'],
        ]);
    }

    /**
     * Login z imienia i nazwiska: "PiotrKowalski".
     */
    private function buildLogin(string $name, string $surname): string
    {
        return preg_replace('/\s+/', '', $name.$surname);
    }
}
