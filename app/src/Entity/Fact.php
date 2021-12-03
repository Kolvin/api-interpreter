<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;

class Fact
{
    public function __construct(private string $id, private Security $security, private Attribute $attribute, private float $value)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $metadata->mapField([
            'id' => true,
            'fieldName' => 'id',
            'type' => 'guid',
            'nullable' => false,
        ]);

        $metadata->mapField([
            'symbol' => true,
            'fieldName' => 'value',
            'type' => 'float',
            'nullable' => false,
        ]);

        $metadata->mapManyToOne([
            'fieldName' => 'security',
            'targetEntity' => Security::class,
            'inversedBy' => 'facts',
        ]);

        $metadata->mapManyToOne([
            'fieldName' => 'attribute',
            'targetEntity' => Attribute::class,
            'inversedBy' => 'facts',
        ]);
    }
}
