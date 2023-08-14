<?php

namespace App\Twig;

use App\Util\RoomUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoomExtension extends AbstractExtension
{
    public function __construct(private RoomUtil $util)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_room_price', [$this->util, 'getPrice']),
            new TwigFunction('app_room_taxe', [$this->util, 'getTaxe']),
            new TwigFunction('app_room_supplement', [$this->util, 'getSupplement']),
            new TwigFunction('app_room_total_price', [$this->util, 'getTotalPrice']),
            new TwigFunction('app_room_price_reduced', [$this->util, 'isPriceReduced']),
            new TwigFunction('app_room_promotion', [$this->util, 'getPromotion']),
            new TwigFunction('app_booking_room_nbr', [$this->util, 'getRoomNumber'])
        ];
    }
}
