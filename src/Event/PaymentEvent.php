<?php

namespace App\Event;

use App\Data\BookingData;
use App\Entity\Commande;

class PaymentEvent
{
    public function __construct(private BookingData $data, private Commande $commande)
    {
    }

    public function getData(): BookingData
    {
        return $this->data;
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }
}

