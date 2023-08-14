<?php

namespace App\Service;

use App\Calculator\PriceCalculator;
use App\Calculator\SupplementPriceCalculator;
use App\Data\BookingData;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Storage\BookingStorage;
use App\Util\BookingDaysCalculator;
use DateTime;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class BookerService
{
    public const INIT_ADULT = 2;
    public const INIT_CHILDREN = 0;
    public const INIT_ROOM = 1;

    public function __construct(
        private BookingRepository $repository,
        private BookingStorage $storage,
        private Security $security,
        private RoomService $roomService,
        private BookingDaysCalculator $daysCalculator,
        private PriceCalculator $priceCalculator,
        private SupplementPriceCalculator $supplementPriceCalculator
    ) {
    }

    public function createData(Room $room): BookingData
    {
        $data = $this->storage->getBookingData();
        $night = $this->daysCalculator->getDays($data->checkin, $data->checkout);
        $totalPrice = $this->roomService->getPrice($room, $data->checkin, $data->checkout);
        $reduced = $room->getPrice() - ($totalPrice - $this->roomService->getTaxe($room));
        $supplementAmount = $this->supplementPriceCalculator->calculate($room->getSupplements()->toArray());

        $data->roomId = $room->getId();
        $data->night = $night;
        $data->amount = ($this->priceCalculator->calculate($room) * $night * $data->roomNumber) + $supplementAmount;
        $data->taxeAmount = $this->roomService->getTaxe($room) * $night * $data->roomNumber;
        $data->discountAmount = $reduced * $night * $data->roomNumber;

        if ($this->security->getUser()) {
            /** @var User|UserInterface $user */
            $user = $this->security->getUser();

            $data->firstname = (string) $user->getFirstname();
            $data->lastname = (string) $user->getLastname();
            $data->email = (string) $user->getEmail();
            $data->phone = (string) $user->getPhone();
            $data->country = (string) $user->getCountry();
            $data->city = (string) $user->getCity();
        }

        return $data;
    }

    public function add(BookingData $bookingData)
    {
        $this->storage->set($bookingData);
    }

    public function roomAvailableForPeriod(array $rooms): array
    {
        $data = $this->storage->init();

        if (empty($rooms) || !$data) {
            return $rooms;
        }

        $results = [];

        /** @var Room $room */
        foreach ($rooms as $room) {
            if ($this->isAvailableForPeriod($room, $data->checkin, $data->checkout)) {
                $results[] = $room;
            }
        }

        return $results;
    }

    public function isAvailableForPeriod(Room $room, DateTime $start, DateTime $end): bool
    {
        $available = $this->repository->availableForPeriod($room, $start, $end);

        return ($room->getRoomNumber() > $available);
    }

    public function today(): DateTime
    {
        return new DateTime();
    }

    public function tomorrow(): DateTime
    {
        return (new DateTime())->modify('+1 day');
    }

    public function adjustDate(BookingData $data): BookingData
    {
        $data->checkin->modify("+{$this->storage->getCheckinMin()} minutes");
        $data->checkout->modify("+{$this->storage->getCheckoutMin()} minutes");

        return $data;
    }
}

