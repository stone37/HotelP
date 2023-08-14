<?php

namespace App\Data;

class CommandeCrudData extends AutomaticCrudData
{
    public function getEntity(): object
    {
        return $this->entity;
    }
}
