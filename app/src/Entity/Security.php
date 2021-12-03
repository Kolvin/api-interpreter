<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadata;

class Security
{
    /**
     * @param string     $id
     * @param string     $symbol
     * @param Collection $facts
     */
    public function __construct(private string $id, private string $symbol, private Collection $facts)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getFacts(): Collection
    {
        return $this->facts;
    }

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $metadata->mapField([
            'id' => true,
            'fieldName' => 'id',
            'type' => 'integer',
            'nullable' => false,
        ]);

        $metadata->mapField([
            'symbol' => true,
            'fieldName' => 'symbol',
            'type' => 'string',
            'nullable' => false,
        ]);

        $metadata->mapOneToMany([
            'fieldName' => 'facts',
            'targetEntity' => Fact::class,
            'mappedBy' => 'security',
        ]);
    }
}
