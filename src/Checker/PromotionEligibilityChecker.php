<?php

namespace App\Checker;

use App\Criteria\PromotionCriteria;
use App\Entity\Promotion;
use DateTime;
use JetBrains\PhpStorm\Pure;

class PromotionEligibilityChecker
{
    public function __construct(private PromotionCriteria $criteria)
    {
    }

    #[Pure] public function isPromotionEligible(Promotion $promotion, DateTime $start, DateTime $end): bool
    {
        if (!$this->criteria->verify($promotion, $start, $end)) {
            return false;
        }

        return true;
    }
}

