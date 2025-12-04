<?php

namespace App\Services\Import;

use SplFileObject;

class CsvReader
{
    /**
     * Czyta plik CSV i zwraca generator:
     * - pierwszy yield: ['type' => 'header', 'data' => array]
     * - kolejne: ['type' => 'row', 'data' => array]
     */
    public function read(string $path, string $delimiter = ';'): \Generator
    {
        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
        $file->setCsvControl($delimiter);

        $header = null;

        while (! $file->eof()) {
            $row = $file->fgetcsv();

            if ($row === [null] || $row === false) {
                continue;
            }

            if ($header === null) {
                $header = $this->normalizeHeader($row);

                yield [
                    'type' => 'header',
                    'data' => $header,
                ];

                continue;
            }

            yield [
                'type' => 'row',
                'data' => $this->mapRowToHeader($header, $row),
            ];
        }
    }


    private function normalizeHeader(array $row): array
    {
        return array_map(
            fn ($value) => trim(str_replace("\xEF\xBB\xBF", '', (string) $value)),
            $row
        );
    }

    private function mapRowToHeader(array $header, array $row): array
    {
        $data = [];

        foreach ($header as $i => $key) {
            $data[$key] = $row[$i] ?? null;
        }

        return $data;
    }
}
