<?php

namespace App\Twig;

use App\Entity\Room;
use App\Manager\PromotionManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PromotionExtension extends AbstractExtension
{
    public function __construct(private PromotionManager $manager)
    {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('get_promotion', array($this, 'getPromotion'))];
    }

    public function getPromotion(Room $room): int
    {
       return $this->manager->getRoomPromotion($room);
    }
}