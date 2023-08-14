<?php

namespace App\Service;

use App\Auth\AuthService;
use App\Entity\User;
use App\Event\UserDeleteRequestEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class DeleteAccountService
{
    public const DAYS = 5;

    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        private AuthService $authService
    )
    {
    }

    /** 
     * @param User $user
     * @param Request $request
     * @throws Exception
     */
    public function deleteUser(User $user, Request $request): void
    {
        $this->authService->logout($request);
        $this->dispatcher->dispatch(new UserDeleteRequestEvent($user));

        $user->setDeleteAt(new DateTimeImmutable('+ '. self::DAYS .' days'));

        $this->em->flush();
    }
}
