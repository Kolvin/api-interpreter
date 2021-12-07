<?php

namespace App\Command;

use App\Entity\Attribute;
use App\Entity\Fact;
use App\Entity\Security;
use App\Repository\Interfaces\AttributeRepositoryInterface;
use App\Repository\Interfaces\FactRepositoryInterface;
use App\Repository\Interfaces\SecurityRepositoryInterface;
use App\Services\File\FileParserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ImportCommand extends Command
{
    public const ATTRIBUTES_FILENAME = 'attributes';
    public const SECURITY_FILENAME = 'securities';
    public const FACTS_FILENAME = 'facts';
    /**
     * @var array|string[]
     */
    private array $options = [
        'file_location' => '/../Data/',
    ];

    public function __construct(string $name = null, private FileParserInterface $fileParser, private AttributeRepositoryInterface $attributeRepository, private SecurityRepositoryInterface $securityRepository, private FactRepositoryInterface $factRepository)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('app:import:data')
            ->setDescription('This command imports the CSV data src/Data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();

        $finder->files()->in(__DIR__.$this->options['file_location']);

        // process facts after parent entities
        $finder->files()->sort(function ($file) {
            return self::FACTS_FILENAME == $file->getFilenameWithoutExtension();
        });

        if (!$finder->hasResults()) {
            return Command::FAILURE;
        }

        foreach ($finder as $file) {
            $fileNameNoExtension = $file->getFilenameWithoutExtension();

            $output->writeln('Processing: '.$fileNameNoExtension);

            try {
                match ($fileNameNoExtension) {
                    self::ATTRIBUTES_FILENAME => $this->fileParser->parseCSV(filePath: (string) $file->getRealPath(), fn: function ($chunk) use ($output) {
                        if ($chunk) {
                            $this->createAttribute($output, $chunk);
                        }
                    }),
                    self::SECURITY_FILENAME => $this->fileParser->parseCSV(filePath: (string) $file->getRealPath(), fn: function ($chunk) use ($output) {
                        if ($chunk) {
                            $this->createSecurity($output, $chunk);
                        }
                    }),
                    self::FACTS_FILENAME => $this->fileParser->parseCSV(filePath: (string) $file->getRealPath(), fn: function ($chunk) use ($output) {
                        if ($chunk) {
                            $this->assignFacts($output, $chunk);
                        }
                    }
                    ),
                    default => 'file not supported yet',
                };
            } catch (\UnhandledMatchError $exception) {
                $output->writeln($exception->getMessage());
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param array<string> $chunk
     */
    private function createAttribute(OutputInterface $output, array $chunk): void
    {
        $attribute = new Attribute(id: $chunk[0], name: $chunk[1], facts: new ArrayCollection());
        $newAttribute = $this->attributeRepository->save($attribute);

        $output->writeln('new security created: '.$newAttribute->getId());
    }

    /**
     * @param array<string> $chunk
     */
    private function createSecurity(OutputInterface $output, array $chunk): void
    {
        $security = new Security(id: $chunk[0], symbol: $chunk[1], facts: new ArrayCollection());
        $newSecurity = $this->securityRepository->save($security);

        $output->writeln('new security created: '.$newSecurity->getId());
    }

    /**
     * @param array<string> $chunk
     */
    private function assignFacts(OutputInterface $output, array $chunk): void
    {
        $securityID = $chunk[0];
        $attributeID = $chunk[1];
        $value = $chunk[1];

        $security = $this->securityRepository->find($securityID);
        $attribute = $this->attributeRepository->find($attributeID);

        if (is_null($security) || is_null($attribute)) {
            $output->writeln('invalid id relations between security: '.$securityID.' and attribute: '.$attributeID.' with fact value: '.$value);

            return;
        }

        $fact = new Fact(id: Uuid::uuid4()->toString(), security: $security, attribute: $attribute, value: floatval($value));
        $newFact = $this->factRepository->save($fact);

        $output->writeln('new fact created between security: '.$security->getSymbol().' and attribute: '.$attribute->getName().' with fact value: '.$newFact->getValue());
    }
}
