<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;

class Security
{
    /**
     * @param string                     $id
     * @param string                     $symbol
     * @param ArrayCollection<int, Fact>|PersistentCollection<int, Fact> $facts
     */
    public function __construct(private string $id, private string $symbol, private ArrayCollection|PersistentCollection $facts)
    {
        $this->facts = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return ArrayCollection<int, Fact>
     */
    public function getFacts(): ArrayCollection
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

//    public static function loadValidatorMetadata(ValidationMetadata $metadata)
//    {
//        $metadata->addPropertyConstraint('id', new NotBlank());
//    }
}
