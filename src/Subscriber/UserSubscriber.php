<?php

namespace App\Subscriber;

use App\Event\UserBannedEvent;
use App\Repository\BookingRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private BookingRepository $bookingRepository)
    {
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [UserBannedEvent::class => 'cleanUserContent'];
    }

    /**
     * @param UserBannedEvent $event
     */
    public function cleanUserContent(UserBannedEvent $event): void
    {
        $this->bookingRepository->deleteForUser($event->getUser());
    }
}
