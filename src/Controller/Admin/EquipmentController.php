<?php

namespace App\Controller\Admin;

use App\Data\EquipmentCrudData;
use App\Entity\Equipment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class EquipmentController extends CrudController
{
    protected string $entity = Equipment::class;
    protected string $templatePath = 'equipment';
    protected string $routePrefix = 'app_admin_equipment';
    protected string $createFlashMessage = 'Un équipement a été crée';
    protected string $editFlashMessage = 'Un équipement a été mise à jour';
    protected string $deleteFlashMessage = 'Un équipement a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les équipements ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les équipements n\'a pas pu être supprimée !';

    #[Route(path: '/equipments', name: 'app_admin_equipment_index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/equipments/create', name: 'app_admin_equipment_create')]
    public function create(): RedirectResponse|Response
    {
        $entity = new Equipment();
        $data = new EquipmentCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/equipments/{id}/edit', name: 'app_admin_equipment_edit', requirements: ['id' => '\d+'])]
    public function edit(Equipment $equipment): Response
    {
        $data = new EquipmentCrudData($equipment);

        return $this->crudEdit($data);
    }

    #[Route(path: '/equipments/{id}/move', name: 'app_admin_equipment_move', requirements: ['id' => '\d+'])]
    public function move(Equipment $equipment): RedirectResponse
    {
        return $this->crudMove($equipment);
    }

    #[Route(path: '/equipments/{id}/delete', name: 'app_admin_equipment_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Equipment $equipment): RedirectResponse|JsonResponse
    {
        $data = new EquipmentCrudData($equipment);

        return $this->crudDelete($data);
    }

    #[Route(path: '/equipments/bulk/delete', name: 'app_admin_equipment_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet équipement ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' équipements ?';
    }
}

