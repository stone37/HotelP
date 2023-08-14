<?php

namespace App\Subscriber;

use App\Entity\User;
use App\Event\UserCreatedEvent;
use App\Event\PasswordResetTokenCreatedEvent;
use App\Service\DeleteAccountService;
use App\Event\UserDeleteRequestEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class AuthSubscriber implements EventSubscriberInterface
{
    public function __construct(private Mailer $mailer, private SettingsManager $manager)
    {
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordRequest',
            UserCreatedEvent::class => 'onRegister',
            UserDeleteRequestEvent::class => 'onDelete',
        ];
    }

    public function onApiRegister(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($user instanceof User && Request::METHOD_POST === $method) {
            $email = $this->mailer->createEmail('mails/auth/register.twig', [
                'user' => $user,
            ])
                ->to($user->getEmail())
                ->subject($this->manager->get()->getName().' | Confirmation du compte');

            $this->mailer->send($email);
        }

        if ($user instanceof User && Request::METHOD_PUT === $method && $user->getDeleteAt()) {
            $email = $this->mailer->createEmail('mails/auth/delete.twig', [
                'user' => $user,
                'days' => DeleteAccountService::DAYS,
            ])
                ->to($user->getEmail())
                ->subject($this->manager->get()->getName().' | Suppression de votre compte');

            $this->mailer->send($email);
        }
    }

    public function onPasswordRequest(PasswordResetTokenCreatedEvent $event): void
    {
        $email = $this->mailer->createEmail('mails/auth/password_reset.twig', [
            'token' => $event->getToken()->getToken(),
            'id' => $event->getUser()->getId(),
            'username' => $event->getUser(),
        ])
            ->to($event->getUser()->getEmail())
            ->subject($this->manager->get()->getName().' | Réinitialisation de votre mot de passe');

        $this->mailer->send($email);
    }

    public function onRegister(UserCreatedEvent $event): void
    {
        if ($event->isUsingOauth()) {
            return;
        }

        $email = $this->mailer->createEmail('mails/auth/register.twig', ['user' => $event->getUser()])
            ->to($event->getUser()->getEmail())
            ->subject($this->manager->get()->getName().' | Confirmation du compte');

        $this->mailer->send($email);
    }

    public function onDelete(UserDeleteRequestEvent $event): void
    {
        $email = $this->mailer->createEmail('mails/auth/delete.twig', [
            'user' => $event->getUser(),
            'days' => DeleteAccountService::DAYS,
        ])
            ->to($event->getUser()->getEmail())
            ->subject($this->manager->get()->getName().' | Suppression de votre compte');

        $this->mailer->send($email);
    }
}
