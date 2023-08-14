<?php

namespace App\Data;

class GalleryCrudData extends AutomaticCrudData
{
    public function getEntity(): object
    {
        return $this->entity;
    }
}
