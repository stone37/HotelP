<?php

namespace App\Util;

use App\Entity\Promotion;
use App\Entity\Room;
use App\Service\RoomService;
use App\Storage\BookingStorage;
use DateTime;

class RoomUtil
{
    public function __construct(private RoomService $service, private BookingStorage $storage)
    {
    }

    public function getTotalPrice(Room $room): int
    {
        return $this->getPrice($room) + $room->getSupplementPrice() + $this->service->getTaxe($room);
    }

    public function getPrice(Room $room): int
    {
        return $this->service->getPrice($room, $this->getStartDate(), $this->getEndDate());
    }

    public function getTaxe(Room $room): int
    {
        return $this->service->getTaxe($room);
    }

    public function getSupplement(Room $room): int
    {
        return $this->service->getSupplement($room);
    }

    public function getPromotion(Room $room): ?Promotion
    {
        return $this->service->getPromotion($room, $this->getStartDate(), $this->getEndDate());
    }

    public function isPriceReduced(Room $room): bool
    {
        return $this->service->hasPromotion($room, $this->getStartDate(), $this->getEndDate());
    }

    public function getRoomNumber(Room $room): int
    {
        return $this->service->availableForPeriod($room, $this->getStartDate(), $this->getEndDate());
    }

    private function getStartDate(): DateTime
    {
        return $this->storage->getBookingData()->checkin;
    }

    private function getEndDate(): DateTime
    {
        return $this->storage->getBookingData()->checkout;
    }
}