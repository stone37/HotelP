<?php

namespace App\Twig;

use App\Util\BookingDaysCalculator;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BookingDaysExtension extends AbstractExtension
{
    public function __construct(private BookingDaysCalculator $calculator)
    {
    }

    public function getFunctions()
    {
        return [new TwigFunction('app_booking_days', [$this, 'getDays'])];
    }

    public function getDays(DateTime $checkin, DateTime $checkout): int
    {
        return $this->calculator->getDays($checkin, $checkout);
    }
}