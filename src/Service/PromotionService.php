<?php

namespace App\Service;

use App\Entity\Room;
use JetBrains\PhpStorm\Pure;

class PromotionService
{
    #[Pure] public function has(Room $room): bool
    {
        if (!$room->getPromotions()) {return false;}

        return true;
    }
}