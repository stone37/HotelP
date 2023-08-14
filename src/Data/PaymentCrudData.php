<?php

namespace App\Data;


class PaymentCrudData extends AutomaticCrudData
{
    public function getEntity(): object
    {
        return $this->entity;
    }
}
