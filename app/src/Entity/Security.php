<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;

class Security
{
    public function __construct(private string $id, private string $symbol)
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

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $metadata->mapField([
            'id' => true,
            'fieldName' => 'id',
            'type' => 'guid',
            'nullable' => false
        ]);

        $metadata->mapField([
            'symbol' => true,
            'fieldName' => 'symbol',
            'type' => 'string',
            'nullable' => false
        ]);

//        $metadata->mapOneToMany([
//            'fieldName' => 'netPromoterScores',
//            'targetEntity' => NetPromoterScore::class,
//            'mappedBy' => 'user'
//        ]);
    }
//
//    public static function loadValidatorMetadata(ValidationMetadata $metadata)
//    {
//        $metadata->addPropertyConstraint('id', new NotBlank());
//    }
}