<?php

namespace App\Command;

use App\Entity\Attribute;
use App\Entity\Fact;
use App\Entity\Security;
use App\Repository\Interfaces\AttributeRepositoryInterface;
use App\Repository\Interfaces\FactRepositoryInterface;
use App\Repository\Interfaces\SecurityRepositoryInterface;
use App\Services\FileParserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ImportCommand extends Command
{
    private array $options = [
        'file_location' => '/../Data/',
    ];

    public function __construct(string $name = null, private FileParserInterface $fileParser, private AttributeRepositoryInterface $attributeRepository, private SecurityRepositoryInterface $securityRepository, private FactRepositoryInterface $factRepository)
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

            $output->writeln('Processing: ' . $fileNameNoExtension);

            match ($fileNameNoExtension) {
                'attributes' => $this->fileParser->parseCSV(filePath: $file->getRealPath(), fn: function ($chunk) use ($output) {
                    if ($chunk) $this->createAttribute($output, $chunk);
                }),
                'securities' => $this->fileParser->parseCSV(filePath: $file->getRealPath(), fn: function ($chunk) use ($output) {
                    if ($chunk) $this->createSecurity($output, $chunk);
                }),
                'facts' => $this->fileParser->parseCSV(filePath: $file->getRealPath(), fn: function ($chunk) use ($output) {
                    if ($chunk) $this->assignFacts($output, $chunk);
                }),
            };
        }

        return Command::SUCCESS;
    }

    private function createAttribute(OutputInterface $output, array $chunk)
    {
        $attribute = new Attribute(id: $chunk[0], name: $chunk[1], facts: new ArrayCollection());
        $newAttribute = $this->attributeRepository->save($attribute);

        $output->writeln('new security created: ' . $newAttribute->getId());
    }

    private function createSecurity(OutputInterface $output, array $chunk)
    {
        $security = new Security(id: $chunk[0], symbol: $chunk[1], facts: new ArrayCollection());
        $newSecurity = $this->securityRepository->save($security);

        $output->writeln('new security created: ' . $newSecurity->getId());
    }

    private function assignFacts(OutputInterface $output, array $chunk)
    {
        $securityID =  $chunk[0];
        $attributeID =  $chunk[1];
        $value =  $chunk[1];

        $security = $this->securityRepository->find($securityID);
        $attribute = $this->attributeRepository->find($attributeID);

        $fact = new Fact(id: Uuid::uuid4()->toString(), security: $security, attribute: $attribute, value: $value);
        $newFact = $this->factRepository->save($fact);

        $output->writeln('new fact created between security: ' . $security->getSymbol(). ' and attribute: ' . $attribute->getName() . ' with fact value: ' . $newFact->getValue());
    }
}