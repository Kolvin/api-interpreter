<?php

namespace App\Services;

class FileParserService implements FileParserInterface
{
    public function parseCSV(string $filePath, callable $fn): void
    {
        $row = 0;
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                if ($row == 1) {continue;}
                call_user_func_array($fn, [$data]);
            }
            fclose($handle);
        }
    }
}