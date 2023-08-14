<?php

namespace App\Storage;

use App\Data\BookingData;
use App\Manager\SettingsManager;
use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;

class BookingStorage
{
    public function __construct(private SettingsManager $manager, private RequestStack $request)
    {
    }

    public function set(BookingData $data): void
    {
        $entity = $this->adjustDate($data);

        $data = [
            'checkin' => $entity->checkin,
            'checkout' => $entity->checkout,
            'adult' => $entity->adult,
            'children' => $entity->children,
            'room_nbr' => $entity->roomNumber
        ];

        $this->request->getSession()->set($this->provideKey(), $data);
    }

    public function remove(): void
    {
        $this->request->getSession()->remove($this->provideKey());
    }


    public function getBookingData(): BookingData
    {
        if (!$this->has()) {
            return new BookingData();
        }

        return $this->init();
    }

    public function has(): bool
    {
        return $this->request->getSession()->has($this->provideKey());
    }

    public function get(): ?array
    {
        return $this->request->getSession()->get($this->provideKey());
    }

    private function adjustDate(BookingData $data): BookingData
    {
        $checkin = $data->checkin;
        $date = new DateTime();
        $date = $date->setTimestamp($checkin->getTimestamp());

        if ($data->checkout <= $data->checkin) {
            $data->checkout = $checkin->modify('+1 day');
            $data->checkin = $date;
        }

        return $data;
    }

    public function init(): ?BookingData
    {
        if (!$this->has()) {
            return null;
        }

        $session = $this->get();

        $data = new BookingData();
        $data->checkin = $session['checkin'];
        $data->checkout = $session['checkout'];
        $data->adult = $session['adult'];
        $data->children = $session['children'];
        $data->roomNumber = $session['room_nbr'];

        return $data;
    }

    public function getCheckinMin(): float|int
    {
        return (((int) $this->manager->get()->getCheckinTime()->format('H') * 60) + (int) $this->manager->get()->getCheckinTime()->format('i'));
    }

    public function getCheckoutMin(): float|int
    {
        return (((int) $this->manager->get()->getCheckoutTime()->format('H') * 60) + (int) $this->manager->get()->getCheckoutTime()->format('i'));
    }

    private function provideKey(): string
    {
        return '_app_booking';
    }
}