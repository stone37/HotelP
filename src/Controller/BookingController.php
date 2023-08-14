<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Data\BookingData;
use App\Entity\Room;
use App\Form\BookingDataType;
use App\Form\BookingType;
use App\Form\DiscountType;
use App\Manager\OrderManager;
use App\Repository\CommandeRepository;
use App\Service\BookerService;
use App\Service\RoomService;
use App\Service\Summary;
use App\Storage\BookingStorage;
use App\Storage\CartStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BookingController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private BookerService $booker,
        private RoomService $roomService,
        private OrderManager $manager,
        private Breadcrumbs $breadcrumbs,
        private BookingStorage $storage,
        private CommandeRepository $commandeRepository,
        private BookingStorage $bookingStorage,
        private CartStorage $cartStorage
    )
    {
    }

    #[Route(path: '/reservation', name: 'app_booking_index')]
    public function index(Request $request): RedirectResponse|Response
    {
        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Hébergements', $this->generateUrl('app_room_index'))
            ->addItem('Réservation');

        $room = $this->roomService->getRoom();
        $booking = $this->booker->createData($room);

        $prepareCommande = $this->forward('App\Controller\CommandeController::prepareCommande', ['data' => $booking]);

        $commande = $this->commandeRepository->find($prepareCommande->getContent());
        $summary = new Summary($commande);

        $bookingForm = $this->createForm(BookingType::class, $booking);
        $discountForm = $this->createForm(DiscountType::class, $commande);

        $bookingForm->handleRequest($request);

        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {
            $this->storage->set($booking);

            return $this->redirectToRoute('app_commande_pay');
        } else if ($bookingForm->isSubmitted()) {
            $this->addFlash('error', 'Un ou plusieurs champs n\'ont pas été renseigne');
        }

        return $this->render('site/booking/index.html.twig', [
            'booking_form' => $bookingForm->createView(),
            'discount_form' => $discountForm->createView(),
            'commande' => $summary,
            'booking' => $booking,
            'room' => $room
        ]);
    }

    #[Route(path: '/reservation/search', name: 'app_booking_search')]
    public function search(Request $request): RedirectResponse|Response
    {
        $data = new BookingData();

        $form = $this->createForm(BookingDataType::class, $data, [
            'action' => $this->generateUrl('app_booking_search')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->booker->add($data);

            return $this->redirectToRoute('app_room_index', ['adult' => $data->adult, 'children' => $data->children]);
        } else if ($form->isSubmitted()) {
            return $this->redirectToRoute('app_room_index');
        }

        return $this->render('site/booking/search.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/reservation/{id}/select', name: 'app_booking_select', requirements: ['id' => '\d+'])]
    public function select(Request $request, Room $room): RedirectResponse|JsonResponse
    {
        $data = $this->bookingStorage->getBookingData();

        $form = $this->createForm(BookingDataType::class, $data, [
            'action' => $this->generateUrl('app_booking_select', ['id' => $room->getId()])
        ]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->booker->add($data);
                $this->cartStorage->add($room);

                if ($this->booker->isAvailableForPeriod($room, $data->checkin, $data->checkin)) {
                    return $this->redirectToRoute('app_booking_index');
                } else {
                    $this->addFlash('error', "L'hébergement que vous aviez choisis est complet sur cette période.");

                    $url = $request->request->get('referer');

                    return new RedirectResponse($url);
                }
            }
        }

        $render = $this->render('site/booking/select.html.twig', [
            'form' => $form->createView(),
            'room' => $room,
            'state' => $request->query->get('state')
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/reservation/{id}/check', name: 'app_booking_check', requirements: ['id' => '\d+'])]
    public function check(Room $room): RedirectResponse
    {
        $this->cartStorage->add($room);

        return $this->redirectToRoute('app_booking_index');
    }
}
