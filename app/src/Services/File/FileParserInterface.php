<?php

declare(strict_types=1);

namespace App\Services\File;

interface FileParserInterface
{
    /**
     * pass csv file location along with {fn} to process each line.
     *
     * @return mixed
     */
    public function parseCSV(string $filePath, callable $fn);
}
