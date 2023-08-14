<?php

namespace App\Data;

use App\Entity\Room;
use App\Form\PromotionType;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class PromotionCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public ?string $name = null;

    public ?string $description = null;

    public ?DateTimeInterface $start = null;

    #[Assert\GreaterThan(propertyPath: 'start')]
    public ?DateTimeInterface $end = null;

    public ?int $discount = null;

    public ?bool $enabled = true;

    public ?Room $room = null;

    #[Assert\File(maxSize: '8M')]
    public ?UploadedFile $file = null;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return PromotionType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setDescription($this->description)
            ->setStart($this->start)
            ->setEnd($this->end)
            ->setDiscount($this->discount)
            ->setRoom($this->room)
            ->setEnabled($this->enabled)
            ->setFile($this->file);
    }
}