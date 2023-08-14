<?php

namespace App\Data;

class RoomGalleryCrudData extends AutomaticCrudData
{
    public function getEntity(): object
    {
        return $this->entity;
    }
}
