<?php

namespace App\Controller\Admin;

use App\Data\BookingCrudData;
use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use App\Event\BookingCancelledEvent;
use App\Event\BookingConfirmedEvent;
use App\Form\Filter\AdminBookingType;
use App\Manager\BookingManager;
use App\Model\BookingSearch;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class BookingController extends CrudController
{
    protected string $entity = Booking::class;
    protected string $templatePath = 'booking';
    protected string $routePrefix = 'app_admin_booking';
    protected string $deleteFlashMessage = 'Une reservation a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les reservations ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les reservations n\'a pas pu être supprimé !';

    #[Route(path: '/bookings', name: 'app_admin_booking_index')]
    public function index(Request $request): Response
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getAdmins($search);

        return $this->crudIndex($query, $form, 1);
    }

    #[Route(path: '/bookings/confirmed', name: 'app_admin_booking_confirmed_index')]
    public function confirm(Request $request): Response
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getConfirmAdmins($search);

        return $this->crudIndex($query, $form, 2);
    }

    #[Route(path: '/bookings/cancelled', name: 'app_admin_booking_cancel_index')]
    public function cancel(Request $request, BookingManager $manager): Response
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);

        $form->handleRequest($request);

        $manager->cancelledAjustement($this->getRepository()->getCancel());

        $query = $this->getRepository()->getCancelAdmins($search);

        return $this->crudIndex($query, $form, 3);
    }

    #[Route(path: '/bookings/archive', name: 'app_admin_booking_archive_index')]
    public function archive(Request $request): Response
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);

        $form->handleRequest($request);

        $query = $this->getRepository()->getArchiveAdmins($search);

        return $this->crudIndex($query, $form, 4);
    }

    #[Route(path: '/bookings/{id}/show/{type}', name: 'app_admin_booking_show', requirements: ['id' => '\d+'])]
    public function show(Booking $booking, $type): Response
    {
        return $this->render('admin/booking/show.html.twig', ['booking' => $booking, 'type' => $type]);
    }

    #[Route(path: '/bookings/{id}/user', name: 'app_admin_booking_user', requirements: ['id' => '\d+'])]
    public function user(Request $request, User $user): Response
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getAdminByUser($user, $search);

        return $this->crudIndex($query, $form);
    }

    #[Route(path: '/bookings/{id}/room', name: 'app_admin_booking_room', requirements: ['id' => '\d+'])]
    public function room(Request $request, Room $room): Response
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getAdminByRoom($room, $search);

        return $this->crudIndex($query, $form);
    }

    #[Route(path: '/bookings/{id}/confirmed', name: 'app_admin_booking_confirmed', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function confirmed(Request $request, BookingManager $manager, Booking $booking): RedirectResponse|JsonResponse
    {
        $form = $this->confirmedForm($booking);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $manager->confirm($booking);

                $this->dispatcher->dispatch(new BookingConfirmedEvent($booking));

                $this->addFlash('success', 'La reservation a été confirmer');
            } else {
                $this->addFlash('error', 'Désolé, la reservation n\'a pas pu être confirmer !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir confirmer cette reservation ?';

        $render = $this->render('ui/modal/_confirm.html.twig', [
            'form' => $form->createView(),
            'data' => $booking,
            'message' => $message,
            'configuration' => $this->getConfiguration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/bookings/bulk/confirmed', name: 'app_admin_booking_bulk_confirmed', options: ['expose' => true])]
    public function confirmedBulk(Request $request, BookingManager $manager): RedirectResponse|JsonResponse
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data')) {
            $request->getSession()->set('data', $ids);
        }

        $form = $this->confirmedMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $booking = $this->getRepository()->find($id);

                    $manager->confirm($booking);

                    $this->dispatcher->dispatch(new BookingConfirmedEvent($booking));
                }

                $this->addFlash('success', 'Les reservations ont été confirmer');
            } else {
                $this->addFlash('error', 'Désolé, les reservations n\'ont pas pu être confirmer !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1) {
            $message = 'Être vous sur de vouloir confirmer ces '.count($ids).' reservations ?';
        } else {
            $message = 'Être vous sur de vouloir confirmer cette reservation ?';
        }

        $render = $this->render('ui/modal/_confirm_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->getConfiguration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/bookings/{id}/cancelled', name: 'app_admin_booking_cancelled', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function cancelled(Request $request, BookingManager $manager, Booking $booking): RedirectResponse|JsonResponse
    {
        $form = $this->cancelledForm($booking);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $manager->cancel($booking);

                $this->dispatcher->dispatch(new BookingCancelledEvent($booking));

                $this->addFlash('success', 'La reservation a été annuler');
            } else {
                $this->addFlash('error', 'Désolé, la reservation n\'a pas pu être annuler !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir annuler cette reservation ?';

        $render = $this->render('ui/modal/_cancel.html.twig', [
            'form' => $form->createView(),
            'data' => $booking,
            'message' => $message,
            'configuration' => $this->getConfiguration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/bookings/bulk/cancelled', name: 'app_admin_booking_bulk_cancelled', options: ['expose' => true])]
    public function cancelledBulk(Request $request, BookingManager $manager): RedirectResponse|JsonResponse
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data')) {
            $request->getSession()->set('data', $ids);
        }

        $form = $this->cancelledMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $booking = $this->getRepository()->find($id);

                    $manager->cancel($booking);

                    $this->dispatcher->dispatch(new BookingCancelledEvent($booking));
                }

                $this->addFlash('success', 'Les reservations ont été annuler');
            } else {
                $this->addFlash('error', 'Désolé, les reservations n\'ont pas pu être annuler !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1) {
            $message = 'Être vous sur de vouloir annuler ces '.count($ids).' reservations ?';
        } else {
            $message = 'Être vous sur de vouloir annuler cette reservation ?';
        }

        $render = $this->render('ui/modal/_cancel_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->getConfiguration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/bookings/{id}/delete', name: 'app_admin_booking_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Booking $booking): RedirectResponse|JsonResponse
    {
        $data = new BookingCrudData($booking);

        return $this->crudDelete($data);
    }

    #[Route(path: '/admin/bookings/bulk/delete', name: 'app_admin_booking_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    private function confirmedForm(Booking $booking): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_confirmed', ['id' => $booking->getId()]))
            ->getForm();
    }

    private function confirmedMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_bulk_confirmed'))
            ->getForm();
    }

    private function cancelledForm(Booking $booking): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_cancelled', ['id' => $booking->getId()]))
            ->getForm();
    }

    private function cancelledMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_bulk_cancelled'))
            ->getForm();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette reservation ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' reservations ?';
    }

    #[ArrayShape(['modal' => "\string[][]"])] protected function getConfiguration(): array
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ],
                'confirmed' => [
                    'type' => 'modal-success',
                    'icon' => 'fas fa-check',
                    'yes_class' => 'btn-outline-success',
                    'no_class' => 'btn-success'
                ],
                'cancelled' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ],
            ]
        ];
    }
}


