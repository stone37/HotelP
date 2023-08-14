<?php

namespace App\Twig;

use App\Util\PriceUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PriceExtension extends AbstractExtension
{
    public function __construct(private PriceUtil $util)
    {
    }

    public function getFunctions()
    {
        return [new TwigFunction('app_calculate_price', array($this->util, 'getPrice'))];
    }
}

