<?php

namespace App\Calculator;

use App\Entity\Room;

class PriceCalculator
{
    public function __construct(private TaxCalculator $taxCalculator)
    {
    }

    public function calculate(Room $room): int
    {
        if (!$room->getTaxe()) {
            return $room->getPrice();
        }

        $taxe = $this->taxCalculator->calculate($room->getPrice(), $room->getTaxe());

        return $room->getPrice() + $taxe;
    }
}