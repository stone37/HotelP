<?php

namespace App\Event;

use App\Entity\User;

class UserBannedEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}

