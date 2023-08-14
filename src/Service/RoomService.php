<?php

namespace App\Service;

use App\Calculator\PromotionPriceCalculator;
use App\Calculator\SupplementPriceCalculator;
use App\Calculator\TaxCalculator;
use App\Checker\PromotionEligibilityChecker;
use App\Entity\Promotion;
use App\Entity\Room;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use App\Storage\CartStorage;
use DateTime;
use JetBrains\PhpStorm\Pure;

class RoomService
{
    public function __construct(
        private RoomRepository $repository,
        private BookingRepository $bookingRepository,
        private CartStorage $storage,
        private TaxCalculator $taxCalculator,
        private PromotionPriceCalculator $promotionPriceCalculator,
        private SupplementPriceCalculator $supplementPriceCalculator,
        private PromotionEligibilityChecker $promotionEligibilityChecker
    )
    {
    }

    public function getRoom(): ?Room
    {
        $cart = $this->storage->get();

        return $this->repository->findOneBy(['id' => $cart['_room_id'], 'enabled' => true]);
    }

    #[Pure] public function hasPromotion(Room $room, DateTime $start, DateTime $end): bool
    {
        $response = false;

        foreach ($room->getPromotions() as $promotion) {
            $response = $this->promotionEligibilityChecker->isPromotionEligible($promotion, $start, $end);
        }

        return $response;
    }

    #[Pure] public function getPromotion(Room $room, DateTime $start, DateTime $end): ?Promotion
    {
        $response = null;

        foreach ($room->getPromotions() as $promotion) {
            if ($this->promotionEligibilityChecker->isPromotionEligible($promotion, $start, $end)) {
                $response = $promotion;
            }
        }

        return $response;
    }

    #[Pure] public function getPrice(Room $room, DateTime $start, DateTime $end): int
    {
        $promotion = $this->getPromotion($room, $start, $end);

        $price = $room->getPrice();

        if ($promotion) {
            $price = $this->promotionPriceCalculator->calculate($room->getPrice(), $promotion->getDiscount());
        }

        return $price;
    }

    public function getTaxe(Room $room): int
    {
        if (!$room->getTaxe()) {
            return 0;
        }

        return $this->taxCalculator->calculate($room->getPrice(), $room->getTaxe());
    }

    public function getSupplement(Room $room): int
    {
        $price = 0;

        if (!$room->getSupplements()->isEmpty()) {
            $price = $this->supplementPriceCalculator->calculate($room->getSupplements()->toArray());
        }

        return $price;
    }

    public function availableForPeriod(Room $room, DateTime $start, DateTime $end): int
    {
        return $this->bookingRepository->availableForPeriod($room, $start, $end);
    }
}
