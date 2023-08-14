<?php

namespace App\Controller\Admin;

use App\Data\RoomGalleryCrudData;
use App\Entity\Room;
use App\Entity\RoomGallery;
use App\Form\RoomGalleryType;
use App\Service\RoomGalleryService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class RoomGalleryController extends CrudController
{
    protected string $entity = RoomGallery::class;
    protected string $templatePath = 'roomGallery';
    protected string $routePrefix = 'app_admin_room_gallery';
    protected string $deleteFlashMessage = 'Une image a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les images ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les images n\'a pas pu être supprimée !';

    #[Route(path: '/rooms/{id}/galleries', name: 'app_admin_room_gallery_index', requirements: ['id' => '\d+'])]
    public function index(Room $room): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->where('row.room = :room')
            ->setParameter('room', $room)
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query, null, $room->getId());
    }

    #[Route(path: '/rooms/{id}/galleries/add', name: 'app_admin_room_gallery_add', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function add(Request $request, RoomGalleryService $service, Room $room): RedirectResponse|Response
    {
        $service->initialize($request);

        $gallery = new RoomGallery();
        $form = $this->createForm(RoomGalleryType::class, $gallery);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $value = $service->add($room);

            if ($value) {
                $this->addFlash('success', 'Image(s) ajouter à la galerie');
            } else {
                $this->addFlash('info', 'Aucune image sélectionner');
            }

            return $this->redirectToRoute('app_admin_room_gallery_index', ['room_id' => $room->getId()]);
        }

        return $this->render('admin/roomGallery/add.html.twig', ['form' => $form->createView(), 'room' => $room]);
    }

    #[Route(path: '/rooms/{room_id}/galleries/{id}/move', name: 'app_admin_room_gallery_move', requirements: ['id' => '\d+', 'room_id' => '\d+'])]
    public function move(RoomGallery $gallery, $room_id): RedirectResponse
    {
        return $this->crudMove($gallery, $room_id);
    }

    #[Route(path: '/room-gallery/{id}/delete', name: 'app_admin_room_gallery_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(RoomGallery $gallery): RedirectResponse|JsonResponse
    {
        $data = new RoomGalleryCrudData($gallery);

        return $this->crudDelete($data);
    }

    #[Route(path: '/admin/room-gallery/bulk/delete', name: 'app_admin_room_gallery_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette image ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' images ?';
    }
}
