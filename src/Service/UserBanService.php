<?php

namespace App\Service;

use App\Event\UserBannedEvent;
use App\Entity\User;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserBanService
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function ban(User $user): void
    {
        $user->setBannedAt(new DateTime());
        $this->dispatcher->dispatch(new UserBannedEvent($user));
    }
}


