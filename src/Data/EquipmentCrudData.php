<?php

namespace App\Data;

use App\Form\EquipmentType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class EquipmentCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $name = null;

    public ?string $description = null;

    public Collection $values;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return EquipmentType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setDescription($this->description);

        $this->values->map(function ($value) {
            $this->entity->addValue($value);
        });
    }
}
