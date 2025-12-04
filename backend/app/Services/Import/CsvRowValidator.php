<?php

namespace App\Services\Import;

class CsvRowValidator
{
    /**
     * Wymagane kolumny w CSV.
     */
    private array $required = [
        'patientId',
        'patientName',
        'patientSurname',
        'patientSex',
        'patientBirthDate',
        'orderId',
        'testName',
        'testValue',
        'testReference',
    ];

    /**
     * Zwraca tablicę brakujących pól.
     * Pusta tablica = wiersz poprawny.
     */
    public function validate(array $row): array
    {
        $missing = [];

        foreach ($this->required as $field) {
            if (! isset($row[$field]) || trim((string) $row[$field]) === '') {
                $missing[] = $field;
            }
        }

        return $missing;
    }

    /**
     * Walidacja nagłówka – rzuca wyjątek jeśli brakuje kolumn.
     */
    public function validateHeader(array $header): void
    {
        $missing = [];

        foreach ($this->required as $field) {
            if (! in_array($field, $header, true)) {
                $missing[] = $field;
            }
        }

        if ($missing !== []) {
            throw new \RuntimeException(
                'Nieprawidłowy nagłówek CSV, brakujące kolumny: '.implode(', ', $missing)
            );
        }
    }
}
