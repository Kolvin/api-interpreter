<?php

declare(strict_types=1);

namespace App\Services;

interface FileParserInterface
{
    /**
     * pass csv file location along with {fn} to process each line
     * @param string $filePath
     * @param callable $fn
     * @return mixed
     */
    public function parseCSV(string $filePath, callable $fn);
}