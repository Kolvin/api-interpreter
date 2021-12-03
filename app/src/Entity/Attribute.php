<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;

class Attribute
{
    /**
     * @param string                     $id
     * @param string                     $name
     * @param ArrayCollection<int, Fact>|PersistentCollection<int, Fact> $facts
     */
    public function __construct(private string $id, private string $name, private ArrayCollection|PersistentCollection $facts)
    {
        $this->facts = new ArrayCollection();
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

//    public static function loadValidatorMetadata(ValidationMetadata $metadata)
//    {
//        $metadata->addPropertyConstraint('id', new NotBlank());
//    }
}
