<?php

namespace App\Manager;

use App\Entity\Room;
use App\Repository\PromotionRepository;

class PromotionManager
{
    public function __construct(private PromotionRepository $repository)
    {
    }

    public function hasRoomPromotion(Room $room): bool
    {
        return !(($this->repository->getByRoom($room) === 0));
    }

    public function getRoomPromotion(Room $room): int
    {
        return $this->repository->getByRoom($room);
    }
}
