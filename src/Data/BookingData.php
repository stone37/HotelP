<?php

namespace App\Data;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\BookerService as Booker;
use Symfony\Component\Validator\Constraints as Assert;

class BookingData
{
    public DateTime $checkin;

    public DateTime $checkout;

    public int $adult = Booker::INIT_ADULT;

    public int $children = Booker::INIT_CHILDREN;

    public int $roomNumber = Booker::INIT_ROOM;

    public ?string $message = null;

    #[Assert\NotBlank(groups: ['booking'])]
    public ?string $firstname = null;

    #[Assert\NotBlank(groups: ['booking'])]
    public ?string $lastname = null;

    #[Assert\NotBlank(groups: ['booking'])]
    public ?string $email = null;

    #[Assert\NotBlank(groups: ['booking'])]
    public ?string $phone = null;

    public ?string $country = null;

    public ?string $city = null;

    public ?int $night = null;

    public ?int $amount = null;

    public ?int $taxeAmount = null;

    public ?int $discountAmount = null;

    public ?int $roomId = null;

    public ?int $userId = null;

    #[Assert\Valid(groups: ['booking'])]
    public ArrayCollection $occupants;

    public function __construct()
    {
        $this->checkin = new DateTime();
        $this->checkout = (new DateTime())->modify('+1 day');
        $this->occupants = new ArrayCollection();
    }
}

