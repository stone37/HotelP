<?php

namespace App\Subscriber;

use App\Entity\Settings;
use App\Event\BookingPaymentEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BookingPaymentSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(private Mailer $mailer, SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public static function getSubscribedEvents(): array
    {
        return [BookingPaymentEvent::class => 'onValidate'];
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function onValidate(BookingPaymentEvent $event): void
    {
        $email = $this->mailer->createEmail('mails/commande/validate.twig', ['booking' => $event->getBooking()])
            ->to($event->getBooking()->getEmail())
            ->subject($this->settings->getName() . ' | Validation de votre commande');

        $this->mailer->send($email);
    }
}
