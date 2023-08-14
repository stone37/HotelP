<?php

namespace App\Data;

use App\Form\TaxeType;
use Symfony\Component\Validator\Constraints as Assert;

class TaxeCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?float $value = 0.0;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return TaxeType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setValue($this->value);
    }
}
