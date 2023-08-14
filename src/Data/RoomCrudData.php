<?php

namespace App\Data;

use App\Entity\Taxe;
use App\Form\RoomType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class RoomCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    public ?string $name = null;

    public ?string $smoker = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 0)]
    public ?int $roomNumber = null;

    #[Assert\NotBlank]
    public ?int $price = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1)]
    public ?int $occupant = null;

    public ?int $area = null;

    public ?string $description  = null;

    public ?string $couchage = null;

    public ?bool $enabled = true;

    public ?Taxe $taxe = null;

    public Collection $equipments;

    public Collection $supplements;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return RoomType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setSmoker($this->smoker)
            ->setRoomNumber($this->roomNumber)
            ->setPrice($this->price)
            ->setArea($this->area)
            ->setOccupant($this->occupant)
            ->setDescription($this->description)
            ->setCouchage($this->couchage)
            ->setTaxe($this->taxe)
            /*->addEquipment($this->equipments)
            ->addSupplement($this->supplements)*/
            ->setEnabled($this->enabled);

        $this->equipments->map(function ($equipment) {
            $this->entity->addEquipment($equipment);
        });

        $this->supplements->map(function ($supplement) {
            $this->entity->addSupplement($supplement);
        });
    }
}
