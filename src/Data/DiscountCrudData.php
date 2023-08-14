<?php

namespace App\Data;

use App\Entity\Discount;
use App\Form\DiscountAdminType;
use Symfony\Component\Validator\Constraints as Assert;

class DiscountCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public ?string $code = null;

    #[Assert\NotBlank]
    public ?int $discount = null;

    public ?string $type = Discount::PERCENT_REDUCTION;

    public ?int $utilisation = null;

    public ?bool $enabled = true;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return DiscountAdminType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setCode($this->code)
            ->setDiscount($this->discount)
            ->setType($this->type)
            ->setUtilisation($this->utilisation)
            ->setEnabled($this->enabled)
        ;
    }
}
