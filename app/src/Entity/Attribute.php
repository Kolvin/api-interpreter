<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadata;

class Attribute
{
    /**
     * @param string     $id
     * @param string     $name
     * @param Collection $facts
     */
    public function __construct(private string $id, private string $name, private Collection $facts)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Fact>
     */
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
            'fieldName' => 'name',
            'type' => 'string',
            'nullable' => false,
        ]);

        $metadata->mapOneToMany([
            'fieldName' => 'facts',
            'targetEntity' => Fact::class,
            'mappedBy' => 'attribute',
        ]);
    }
}
