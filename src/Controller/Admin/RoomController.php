<?php

namespace App\Controller\Admin;

use App\Data\RoomCrudData;
use App\Entity\Room;
use App\Form\Filter\AdminRoomType;
use App\Model\RoomSearch;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class RoomController extends CrudController
{
    protected string $entity = Room::class;
    protected string $templatePath = 'room';
    protected string $routePrefix = 'app_admin_room';
    protected string $createFlashMessage = 'Un hébergement a été crée';
    protected string $editFlashMessage = 'Un hébergement a été mise à jour';
    protected string $deleteFlashMessage = 'Un hébergement a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les hébergements de réduction ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les hébergements de réduction n\'a pas pu être supprimé !';

    #[Route(path: '/rooms', name: 'app_admin_room_index')]
    public function index(Request $request): Response
    {
        $search = new RoomSearch();

        $form = $this->createForm(AdminRoomType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getAdmins($search);

        return $this->crudIndex($query, $form);
    }

    #[Route(path: '/rooms/show/{id}', name: 'app_admin_room_show', requirements: ['id' => '\d+'])]
    public function show(Room $room): Response
    {
       return $this->render('admin/room/show.html.twig', ['room' => $room]);
    }

    #[Route(path: '/rooms/create', name: 'app_admin_room_create')]
    public function create(): RedirectResponse|Response
    {
        $entity = new Room();
        $data = new RoomCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/rooms/{id}/edit', name: 'app_admin_room_edit', requirements: ['id' => '\d+'])]
    public function edit(Room $room): Response
    {
        $data = new RoomCrudData($room);

        return $this->crudEdit($data);
    }

    #[Route(path: '/rooms/{id}/move', name: 'app_admin_room_move', requirements: ['id' => '\d+'])]
    public function move(Room $room): RedirectResponse
    {
        return $this->crudMove($room);
    }

    #[Route(path: '/rooms/{id}/delete', name: 'app_admin_room_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Room $room): RedirectResponse|JsonResponse
    {
        $data = new RoomCrudData($room);

        return $this->crudDelete($data);
    }

    #[Route(path: '/rooms/bulk/delete', name: 'app_admin_room_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet hébergement ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' hébergements ?';
    }
}


