<?php

namespace App\Manager;

use App\Entity\Booking;
use App\Event\BookingCancelledEvent;
use App\Repository\BookingRepository;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BookingManager
{
    public function __construct(
        private BookingRepository $repository,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function confirm(Booking $booking)
    {
        $this->confirmed($booking);
        $this->repository->flush();
    }

    public function cancel(Booking $booking)
    {
        $this->cancelled($booking);
        $this->repository->flush();
    }

    public function cancelledAjustement(array $bookings)
    {
        if (!$bookings) {
            return;
        }

        /** @var Booking $booking */
        foreach($bookings as $booking) {
            if (!($booking->getState() === Booking::CANCELLED)) {
                $this->cancelled($booking);

                $this->dispatcher->dispatch(new BookingCancelledEvent($booking));;
            }
        }

        $this->repository->flush();
    }

    private function confirmed(Booking $booking)
    {
        $booking->setState(Booking::CONFIRMED);
        $booking->setConfirmedAt(new DateTime());
        $booking->setCancelledAt(null);
    }

    private function cancelled(Booking $booking)
    {
        $booking->setState(Booking::CANCELLED);
        $booking->setCancelledAt(new DateTime());
        $booking->setConfirmedAt(null);
    }
}

