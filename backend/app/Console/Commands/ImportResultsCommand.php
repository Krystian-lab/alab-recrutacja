<?php

namespace App\Console\Commands;

use App\Services\Import\ResultsImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportResultsCommand extends Command
{
    /**
     * php artisan results:import {ścieżka} {--delimiter=;}
     */
    protected $signature = 'results:import
                            {file=storage/app/results.csv : Ścieżka do pliku CSV}
                            {--delimiter=; : Separator kolumn w CSV}';

    protected $description = 'Importuje wyniki badań pacjentów z pliku CSV';

    public function __construct(private ResultsImporter $importer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $file = $this->argument('file');
        $delimiter = $this->option('delimiter');

        if (! file_exists($file)) {
            $this->error("Plik {$file} nie istnieje.");
            Log::channel('results_import')->error('Plik CSV nie istnieje', ['path' => $file]);

            return self::FAILURE;
        }

        $this->info("Rozpoczynam import z pliku: {$file}");

        try {
            $result = $this->importer->import($file, $delimiter);
        } catch (\Throwable $e) {
            $this->error('Krytyczny błąd podczas importu: '.$e->getMessage());

            Log::channel('results_import')->error('Krytyczny błąd importu CSV', [
                'file'  => $file,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }

        $this->info("Import zakończony. Poprawne wiersze: {$result['success']}, błędy: {$result['errors']}.");

        return self::SUCCESS;
    }
}
