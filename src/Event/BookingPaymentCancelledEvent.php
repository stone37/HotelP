<?php


namespace App\Event;


use App\Entity\Booking;

class BookingPaymentCancelledEvent
{
    private Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }
}