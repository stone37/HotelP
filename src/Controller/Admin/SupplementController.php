<?php

namespace App\Controller\Admin;

use App\Data\SupplementCrudData;
use App\Entity\Supplement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class SupplementController extends CrudController
{
    protected string $entity = Supplement::class;
    protected string $templatePath = 'supplement';
    protected string $routePrefix = 'app_admin_supplement';
    protected string $createFlashMessage = 'Un supplément a été crée';
    protected string $editFlashMessage = 'Un supplément a été mise à jour';
    protected string $deleteFlashMessage = 'Un supplément a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les suppléments ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les suppléments n\'a pas pu être supprimée !';

    #[Route(path: '/supplements', name: 'app_admin_supplement_index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/supplements/create', name: 'app_admin_supplement_create')]
    public function create(): RedirectResponse|Response
    {
        $entity = new Supplement();
        $data = new SupplementCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/supplements/{id}/edit', name: 'app_admin_supplement_edit', requirements: ['id' => '\d+'])]
    public function edit(Supplement $supplement): RedirectResponse|Response
    {
        $data = new SupplementCrudData($supplement);

        return $this->crudEdit($data);
    }

    #[Route(path: '/supplements/{id}/move', name: 'app_admin_supplement_move', requirements: ['id' => '\d+'])]
    public function move(Supplement $supplement): RedirectResponse
    {
        return $this->crudMove($supplement);
    }

    #[Route(path: '/supplements/{id}/delete', name: 'app_admin_supplement_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Supplement $supplement): RedirectResponse|JsonResponse
    {
        $data = new SupplementCrudData($supplement);

        return $this->crudDelete($data);
    }

    #[Route(path: '/supplements/bulk/delete', name: 'app_admin_supplement_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet supplement ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' supplements ?';
    }
}


