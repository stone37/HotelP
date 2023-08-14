<?php


namespace App\Event;


use App\Entity\Booking;

class BookingPaymentCancelledEvent
{
    public function __construct(private Booking $booking)
    {
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }
}