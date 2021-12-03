<?php

namespace App\Command;

use App\Services\FileParserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ImportCommand extends Command
{
    private array $options = [
        'file_location' => '/../Data/',
    ];

    public function __construct(string $name = null, private FileParserInterface $fileParser)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('app:import:data')
            ->setDescription('This command imports the CSV data src/Data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();

        $finder->files()->in(__DIR__ . $this->options['file_location']);

        // process facts after parent entities
        $finder->files()->sort(function ($file) {
            return $file->getFilenameWithoutExtension() == 'facts';
        });

        if (!$finder->hasResults()) {
            return Command::FAILURE;
        }

        foreach ($finder as $file) {
            $fileNameNoExtension = $file->getFilenameWithoutExtension();

            match ($fileNameNoExtension) {
                'attributes' => $this->fileParser->parseCSV(filePath: $file->getRealPath(), fn: function ($chunk) {
                    $this->createAttribute($chunk);
                }),
                'securities' => $this->fileParser->parseCSV(filePath: $file->getRealPath(), fn: function ($chunk) {
                    $this->createSecurity($chunk);
                }),
                'facts' => $this->fileParser->parseCSV(filePath: $file->getRealPath(), fn: function ($chunk) {
                    $this->assignFacts($chunk);
                }),
            };
        }

        return Command::SUCCESS;
    }

    private function createAttribute($chunk)
    {
        dump($chunk);
    }

    private function createSecurity($chunk)
    {
        dump($chunk);
    }

    private function assignFacts($chunk)
    {
        dump($chunk);
    }
}