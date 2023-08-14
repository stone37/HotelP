<?php

namespace App\Event;

use App\Entity\Booking;
use Symfony\Contracts\EventDispatcher\Event;

class BookingUserCancelledEvent extends Event
{
    public function  __construct(private Booking $booking)
    {
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }
}

