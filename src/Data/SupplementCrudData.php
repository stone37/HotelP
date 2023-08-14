<?php

namespace App\Data;

use App\Entity\Supplement;
use App\Form\SupplementType;
use Symfony\Component\Validator\Constraints as Assert;

class SupplementCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?int $price = null;

    #[Assert\NotBlank]
    public ?int $type = Supplement::PER_PERSON;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return SupplementType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setPrice($this->price)
            ->setType($this->type);
    }
}
