<?php

namespace App\Services\File;

class FileParserService implements FileParserInterface
{
    public function parseCSV(string $filePath, callable $fn): void
    {
        $row = 0;
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                ++$row;
                if (1 == $row) {
                    continue;
                }
                call_user_func_array($fn, [$data]);
            }
            fclose($handle);
        }
    }
}
