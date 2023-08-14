<?php

namespace App\Subscriber;

use App\Event\EmailVerificationEvent;
use App\Mailing\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Email;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProfileSubscriber implements EventSubscriberInterface
{
    public function __construct(private Mailer $mailer)
    {
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [EmailVerificationEvent::class => 'onEmailChange'];
    }

    /**
     * @param EmailVerificationEvent $event
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function onEmailChange(EmailVerificationEvent $event): void
    {
        // On envoie un email pour confirmer le compte
        $email = $this->mailer->createEmail('mails/profile/email-confirmation.twig', ['token' => $event->emailVerification->getToken(), 'firstname' => $event->emailVerification->getAuthor()->getFirstname(),])
            ->to($event->emailVerification->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Mise à jour de votre adresse mail');
        $this->mailer->send($email);

        // On notifie l'utilisateur concernant le changement
        $email = $this->mailer->createEmail('mails/profile/email-notification.twig', ['firstname' => $event->emailVerification->getAuthor()->getFirstname(), 'email' => $event->emailVerification->getEmail()])
            ->to($event->emailVerification->getAuthor()->getEmail())
            ->subject("Demande de changement d'email en attente");
        $this->mailer->send($email);
    }
}
