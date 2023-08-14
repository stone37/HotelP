<?php

namespace App\Calculator;

use App\Entity\Supplement;
use App\Storage\BookingStorage;
use App\Util\BookingDaysCalculator;

class SupplementPriceCalculator
{
    public function __construct(
        private BookingStorage $storage,
        private BookingDaysCalculator $calculator
    )
    {
    }

    public function calculate(array $supplements): int
    {
        $data = $this->storage->getBookingData();

        $occupant = $data->adult + $data->children;
        $night = $this->calculator->getDays($data->checkin, $data->checkout);
        $amount = 0;

        /** @var Supplement $supplement */
        foreach ($supplements as $supplement) {
            if ($supplement->getType() === Supplement::PER_PERSON && $supplement->getPrice()) {
                $amount = ($occupant * $supplement->getPrice());
            } elseif ($supplement->getType() === Supplement::PER_DAY && $supplement->getPrice()) {
                $amount = ($night * $supplement->getPrice());
            } elseif ($supplement->getType() === Supplement::PER_DAY_PERSON && $supplement->getPrice()) {
                $amount = ($night * $occupant * $supplement->getPrice());
            }
        }

        return $amount;
    }
}
