<?php

namespace App\Subscriber;

use App\Entity\Settings;
use App\Event\BookingCancelledEvent;
use App\Event\BookingConfirmedEvent;
use App\Event\BookingUserCancelledEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BookingSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(private Mailer $mailer, SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public static function getSubscribedEvents()
    {
        return [
            BookingConfirmedEvent::class => 'onConfirmed',
            BookingCancelledEvent::class => 'onCancelled',
            BookingUserCancelledEvent::class => 'onUserCancelled'
        ];
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function onConfirmed(BookingConfirmedEvent $event)
    {
        $booking = $event->getBooking();

        $email = $this->mailer->createEmail('mails/booking/confirm.twig', ['booking' => $booking])
            ->to($booking->getEmail())
            ->subject($this->settings->getName() . ' |  Confirmation de réservation #' . $booking->getNumber());

        $this->mailer->send($email);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function onCancelled(BookingCancelledEvent $event)
    {
        $booking = $event->getBooking();

        $email = $this->mailer->createEmail('mails/booking/cancel.twig', ['booking' => $booking])
            ->to($booking->getEmail())
            ->subject($this->settings->getName() . ' | Annulation de votre réservation');

        $this->mailer->send($email);
    }

    public function onUserCancelled(BookingUserCancelledEvent $event)
    {
        /*$booking = $event->getBooking();
        $commande = $booking->getCommande();
        $hostel = $booking->getHostel();
        $summary = new Summary($commande);

        $this->removePayout($commande);

        $cancellation = $this->getCancellation($booking->getCancelation(), $booking->getCheckin());

        if ($cancellation) {
            // Remboursement complet
            $payout = (new CancelPayout())
                ->setCommande($commande)
                ->setState(CancelPayout::PAYOUT_NEW)
                ->setRole(CancelPayout::PAYOUT_ROLE_USER)
                ->setOwner($event->getBooking()->getOwner())
                ->setCurrency($this->settings->getBaseCurrency()->getCode())
                ->setAmount($summary->amountPaid());

            $this->cancelPayoutRepository->add($payout, true);
        } else {
            if ($hostel->getCancellationPolicy()->getResult() === Cancelation::CANCEL_RESULT_FIRST) {
                // Remboursement partiel

                $partnerAmount = $this->roomPriceCalculator->calculate($booking->getRoom()) * $booking->getRoomNumber();
                $partnerTaxeAmount = $this->roomPriceCalculator->getTaxe($booking->getRoom()) * $booking->getRoomNumber();
                $userAmount = $summary->amountPaid() - $partnerAmount;

                // Refund payout
                $cancelPayout = (new CancelPayout())
                    ->setCommande($commande)
                    ->setRole(CancelPayout::PAYOUT_ROLE_USER)
                    ->setState(CancelPayout::PAYOUT_NEW)
                    ->setOwner($event->getBooking()->getOwner())
                    ->setCurrency($this->settings->getBaseCurrency()->getCode())
                    ->setAmount($userAmount);

                // Partner payout
                $payout = (new Payout())
                    ->setCommande($commande)
                    ->setOwner($event->getBooking()->getHostel()->getOwner())
                    ->setState(Payout::PAYOUT_NEW)
                    ->setCurrency($this->settings->getBaseCurrency()->getCode())
                    ->setAmount($this->payoutCalculator->calculate($hostel->getPlan(), $partnerAmount, ($partnerAmount - $partnerTaxeAmount)));

                $this->cancelPayoutRepository->add($cancelPayout, true);

            } else {
                // Aucun remboursement
                $payout = (new Payout())
                    ->setCommande($commande)
                    ->setOwner($event->getBooking()->getHostel()->getOwner())
                    ->setState(Payout::PAYOUT_NEW)
                    ->setCurrency($this->settings->getBaseCurrency()->getCode())
                    ->setAmount($this->payoutCalculator->calculate($hostel->getPlan(), $summary->amountPaid(), $summary->amountCommission()));
            }

            $this->payoutRepository->add($payout, true);
        }

        $email = $this->mailer->createEmail('mails/booking/user-cancel.twig', ['booking' => $event->getBooking()])
            ->to($event->getBooking()->getEmail())
            ->subject($this->settings->getName() . ' | Demande d\'annulation de réservation');

        $this->mailer->send($email);*/
    }
}
