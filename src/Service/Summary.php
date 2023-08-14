<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\Discount;
use JetBrains\PhpStorm\Pure;

class Summary
{
    public function __construct(private Commande $commande)
    {
    }

    #[Pure] public function getBooking()
    {
        return $this->commande->getBooking();
    }

    #[Pure] public function getAmountTotal(): int
    {
        return $this->commande->getAmountTotal();
    }

    #[Pure] public function getAmountBeforeDiscount(): int
    {
        return $this->commande->getAmount();
    }

    #[Pure] public function getTaxeAmount(): int
    {
        return $this->commande->getTaxeAmount();
    }

    #[Pure] public function amountPaid(): ?int
    {
        return ($this->commande->getAmountTotal() - $this->getDiscount());
    }

    #[Pure] public function getDiscount(): int
    {
        $price = 0;
        $discount = $this->commande->getDiscount();

        if ($discount) {
            if ($discount->getType() === Discount::FIXED_PRICE) {
                $price = ($this->getAmountBeforeDiscount() - $discount->getDiscount());
            } elseif ($discount->getType() === Discount::PERCENT_REDUCTION) {
                $price = (($this->getAmountBeforeDiscount() * $discount->getDiscount()) / 100);
            }
        }

        return $price;
    }

    #[Pure] public function hasDiscount(): bool
    {
        return (bool) $this->commande->getDiscount();
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }
}
