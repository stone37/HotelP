<?php

namespace App\Data;

class UserCrudData extends AutomaticCrudData
{
    public function getEntity(): object
    {
        return $this->entity;
    }
}
