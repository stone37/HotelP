<?php

namespace App\Manager;

use App\Entity\Commande;
use App\Entity\Discount;
use App\Event\OrderEvent;
use App\Service\Summary;
use App\Storage\CommandeStorage;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderManager
{
    private Commande $commande;

    public function __construct(
        private Security $security,
        private EventDispatcherInterface $dispatcher,
        private EntityManagerInterface $em,
        private CommandeStorage $storage
    )
    {
        $this->commande = $this->getCurrent();
    }

    public function getCurrent(): Commande
    {
        $commande = $this->storage->getCommande();

        if ($commande !== null) {
            return $commande;
        }

        $commande = new Commande();

        if ($this->security->getUser()) {
            $commande->setUser($this->security->getUser());
        }

        return $commande;
    }

    public function setDiscount(Discount $discount): void
    {
        if ($this->commande) {
            $this->commande->setDiscount($discount);

            $this->dispatcher->dispatch(new OrderEvent($this->commande));
            $this->em->persist($this->commande);

            $this->em->flush();
        }
    }

    #[Pure] public function summary(): Summary
    {
        return new Summary($this->commande);
    }
}