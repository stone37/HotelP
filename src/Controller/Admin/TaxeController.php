<?php

namespace App\Controller\Admin;

use App\Data\TaxeCrudData;
use App\Entity\Taxe;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class TaxeController extends CrudController
{
    protected string $entity = Taxe::class;
    protected string $templatePath = 'taxe';
    protected string $routePrefix = 'app_admin_taxe';
    protected string $createFlashMessage = 'Une taxe a été crée';
    protected string $editFlashMessage = 'Une taxe a été mise à jour';
    protected string $deleteFlashMessage = 'Une taxe a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les taxes ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les taxes n\'a pas pu être supprimé !';

    #[Route(path: '/taxes', name: 'app_admin_taxe_index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/taxes/create', name: 'app_admin_taxe_create')]
    public function create(): RedirectResponse|Response
    {
        $entity = new Taxe();
        $data = new TaxeCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/taxes/{id}/edit', name: 'app_admin_taxe_edit', requirements: ['id' => '\d+'])]
    public function edit(Taxe $taxe): RedirectResponse|Response
    {
        $data = new TaxeCrudData($taxe);

        return $this->crudEdit($data);
    }

    #[Route(path: '/taxes/{id}/delete', name: 'app_admin_taxe_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Taxe $taxe): RedirectResponse|JsonResponse
    {
        $data = new TaxeCrudData($taxe);

        return $this->crudDelete($data);
    }

    #[Route(path: '/taxes/bulk/delete', name: 'app_admin_taxe_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette taxe ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' taxes ?';
    }
}

