<?php

namespace App\Subscriber;

use App\Data\BookingData;
use App\Entity\Booking;
use App\Entity\Payment;
use App\Entity\Room;
use App\Event\BookingPaymentEvent;
use App\Event\PaymentEvent;
use App\Service\Summary;
use App\Service\UniqueNumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaymentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $request,
        private EntityManagerInterface $em,
        private UniqueNumberGenerator $generator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [PaymentEvent::class => 'onPayment'];
    }

    public function onPayment(PaymentEvent $event)
    {
        $data = $event->getData();
        $commande = $event->getCommande();

        $room = $this->em->getRepository(Room::class)->find($data->roomId);

        // On enregistre la reservation
        $booking = (new Booking())
            ->setRoom($room)
            ->setCheckin($data->checkin)
            ->setCheckout($data->checkout)
            ->setAdult($data->adult)
            ->setChildren($data->children)
            ->setDays($data->night)
            ->setAmount($data->amount)
            ->setRoomNumber($data->roomNumber)
            ->setNumber($this->generator->generate(6))
            ->setUser($commande->getUser())
            ->setIp($this->request->getMainRequest()->getClientIp())
            ->setFirstname($data->firstname)
            ->setLastname($data->lastname)
            ->setEmail($data->email)
            ->setPhone($data->phone)
            ->setCountry($data->country)
            ->setCity($data->city)
            ->setMessage($data->message);
        $booking = $this->addOccupants($booking, $data);

        $commande->setBooking($booking)
            ->setValidated(true)
            ->setNumber($this->generator->generate(10, false));

        $summary = new Summary($commande);

        // On enregistre la transaction
        $payment = (new Payment())
            ->setCommande($commande)
            ->setPrice($summary->amountPaid())
            ->setTaxe($summary->getTaxeAmount())
            ->setDiscount($summary->getDiscount())
            ->setEnabled(true)
            ->setFirstname($booking->getFirstname())
            ->setLastname($booking->getLastname())
            ->setCountry($booking->getCountry())
            ->setCity($booking->getCity());

        $this->em->persist($booking);
        $this->em->persist($payment);

        $this->em->flush();

        $this->dispatcher->dispatch(new BookingPaymentEvent($booking));
    }

    private function addOccupants(Booking $booking, BookingData $data): Booking
    {
        if ($data->occupants->isEmpty()) {
            return $booking;
        }

        $data->occupants->map(function ($occupant) use ($booking) {
            $booking->addOccupant($occupant);
        });

        return $booking;
    }
}



