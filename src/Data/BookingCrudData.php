<?php

namespace App\Data;

class BookingCrudData extends AutomaticCrudData
{
    public function getEntity(): object
    {
        return $this->entity;
    }
}
