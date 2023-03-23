<?php

namespace App\Controller\Admin;

use App\Entity\RoomGallery;
use App\Event\AdminCRUDEvent;
use App\Form\RoomGalleryType;
use App\Repository\RoomGalleryRepository;
use App\Repository\RoomRepository;
use App\Service\RoomGalleryService;
use JetBrains\PhpStorm\ArrayShape;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class RoomGalleryController extends AbstractController
{
    public function __construct(
        private RoomGalleryRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher,
        private RoomGalleryService $service,
        private RoomRepository $roomRepository
    )
    {
    }

    #[Route(path: '/rooms/{room_id}/galleries', name: 'app_admin_room_gallery_index', requirements: ['room_id' => '\d+'])]
    public function index(Request $request, $room_id): Response
    {
        $room = $this->roomRepository->find($room_id);
        $qb = $this->repository->findBy(['room' => $room], ['position' => 'asc']);

        $galleries = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/roomGallery/index.html.twig', [
            'galleries' => $galleries,
            'room' => $room
        ]);
    }

    #[Route(path: '/rooms/{room_id}/galleries/add', name: 'app_admin_room_gallery_add', requirements: ['room_id' => '\d+'], options: ['expose' => true])]
    public function add(Request $request, $room_id): RedirectResponse|Response
    {
        $room = $this->roomRepository->find($room_id);

        $this->service->initialize($request);

        $gallery = new RoomGallery();
        $form = $this->createForm(RoomGalleryType::class, $gallery);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $value = $this->service->add($room);

            if ($value) {
                $this->addFlash('success', 'Image(s) ajouter à la galerie');
            } else {
                $this->addFlash('info', 'Aucune image sélectionner');
            }

            return $this->redirectToRoute('app_admin_room_gallery_index', ['room_id' => $room->getId()]);
        }

        return $this->render('admin/roomGallery/add.html.twig', [
            'form' => $form->createView(),
            'room' => $room
        ]);
    }

    #[Route(path: '/rooms/{room_id}/galleries/{id}/move', name: 'app_admin_room_gallery_move', requirements: ['id' => '\d+', 'room_id' => '\d+'])]
    public function move(Request $request, RoomGallery $gallery, $room_id): RedirectResponse
    {
        $room = $this->roomRepository->find($room_id);

        if ($request->query->has('pos')) {
            $pos = ($gallery->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $gallery->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_room_gallery_index', ['room_id' => $room->getId()]);
    }

    #[Route(path: '/room-gallery/{id}/delete', name: 'app_admin_room_gallery_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, RoomGallery $gallery): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($gallery);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($gallery);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($gallery, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'image a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'image n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette image ?';

        $render = $this->render('ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $gallery,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/room-gallery/bulk/delete', name: 'app_admin_room_gallery_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(Request $request): RedirectResponse|JsonResponse
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data')) {
            $request->getSession()->set('data', $ids);
        }

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $gallery = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($gallery), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($gallery, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les images ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les images n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1) {
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' images ?';
        } else {
            $message = 'Être vous sur de vouloir supprimer cette image ?';
        }

        $render = $this->render('ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(RoomGallery $gallery): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_room_gallery_delete', ['id' => $gallery->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_room_gallery_bulk_delete'))
            ->getForm();
    }

    #[ArrayShape(['modal' => "\string[][]"])] private function configuration(): array
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ]
            ]
        ];
    }
}