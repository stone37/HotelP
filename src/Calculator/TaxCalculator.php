<?php


namespace App\Calculator;

use App\Entity\Taxe;

class TaxCalculator
{
    public function calculate(int $base, Taxe $taxe): float
    {
        return round($base * $taxe->getValue()) / 100;
    }
}