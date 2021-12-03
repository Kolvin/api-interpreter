<?php

namespace App\Services;

class FileParserService implements FileParserInterface
{
    public function parseCSV(string $filePath, callable $fn): void
    {
        $handle = fopen($filePath, "r");

        if ($handle){
            while (!feof($handle))
            {
                $header = fgetcsv($handle);
                $chunk = fgetcsv($handle);
                call_user_func_array($fn, [$chunk]);
            }
        }

        fclose($handle);
    }
}