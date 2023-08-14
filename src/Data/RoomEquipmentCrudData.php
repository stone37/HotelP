<?php

namespace App\Data;

use App\Form\RoomEquipmentType;
use Symfony\Component\Validator\Constraints as Assert;

class RoomEquipmentCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $name = null;

    public ?string $description = null;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return RoomEquipmentType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setDescription($this->description);
    }
}
