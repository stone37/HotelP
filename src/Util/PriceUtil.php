<?php

namespace App\Util;

use App\Calculator\PriceCalculator;
use App\Entity\Room;

class PriceUtil
{
    public function __construct(private PriceCalculator $calculator)
    {
    }

    public function getPrice(Room $room): int
    {
        return $this->calculator->calculate($room);
    }
}