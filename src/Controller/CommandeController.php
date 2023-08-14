<?php

namespace App\Controller;

use App\Data\BookingData;
use App\Event\PaymentEvent;
use App\Manager\OrderManager;
use App\Repository\CommandeRepository;
use App\Storage\CommandeStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    public function __construct(
        private OrderManager $manager,
        private CommandeRepository $repository,
        private CommandeStorage $storage,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function prepareCommande(BookingData $data): Response
    {
        $commande = ($this->manager->getCurrent())
                ->setValidated(false)
                ->setNumber(null)
                ->setAmount($data->amount - $data->taxeAmount)
                ->setTaxeAmount($data->taxeAmount)
                ->setDiscountAmount($data->discountAmount)
                ->setAmountTotal($data->amount - $data->discountAmount);

        if (!$this->storage->has()) {
            $this->repository->add($commande);
        }

        $this->repository->flush();

        $this->storage->set($commande->getId());

        return new Response($commande->getId());
    }

    #[Route(path: '/commande/payment', name: 'app_commande_pay')]
    public function payment(Request $request): RedirectResponse
    {
        $commande = $this->manager->getCurrent();

        if (!$commande || $commande->isValidated()) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        if (!$request->getSession()->get('booking') instanceof BookingData) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        $booking = $request->getSession()->get('booking');

        $this->dispatcher->dispatch(new PaymentEvent($booking, $commande));

        $request->getSession()->remove('orderId');
        $request->getSession()->remove('app_cart');
        $request->getSession()->remove('booking');

        return $this->redirectToRoute('app_commande_validated');
    }

    #[Route(path: '/commande/validate/success', name: 'app_commande_validated')]
    public function success(): Response
    {
        return $this->render('site/commande/success.html.twig');
    }
}

