<?php

namespace App\Subscriber;

use App\Event\BookingCancelledEvent;
use App\Event\PaymentRefundedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentRefundedSubscriber
{
    public function __construct(private EventDispatcherInterface $dispatcher, private EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [PaymentRefundedEvent::class => 'onPaymentReimbursed'];
    }

    public function onPaymentReimbursed(PaymentRefundedEvent $event): void
    {
        $payment = $event->getPayment();
        if ($payment->isRefunded()) {
            return;
        }

        $payment->setRefunded(true);
        $this->em->flush();

        $this->dispatcher->dispatch(new BookingCancelledEvent($payment->getCommande()->getBooking()));
    }
}