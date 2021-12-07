<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\FileParserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FileParserServiceTest extends KernelTestCase
{
    private FileParserInterface $service;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->service = self::getContainer()->get(FileParserInterface::class);
    }

    public function testBadFilePath(): void
    {
        $this->expectError();
        $this->service->parseCSV(filePath: 'bad-path', fn: function () {});
    }

    public function testValidFilePath(): void
    {
        $this->service->parseCSV(filePath: __DIR__.'/../../Resources/files/taxables.csv', fn: function ($chunk) {
            $this->assertIsArray($chunk);
        });
    }
}
