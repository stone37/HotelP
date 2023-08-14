<?php

namespace App\Controller\Admin;

use App\Data\DiscountCrudData;
use App\Entity\Discount;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DiscountController extends CrudController
{
    protected string $entity = Discount::class;
    protected string $templatePath = 'discount';
    protected string $routePrefix = 'app_admin_discount';
    protected string $createFlashMessage = 'Un code de réduction a été crée';
    protected string $editFlashMessage = 'Un code de réduction a été mise à jour';
    protected string $deleteFlashMessage = 'Un code de réduction a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les codes de réduction ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les codes de réduction n\'a pas pu être supprimé !';

    #[Route(path: '/discounts', name: 'app_admin_discount_index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/discounts/create', name: 'app_admin_discount_create')]
    public function create(): RedirectResponse|Response
    {
        $entity = new Discount();
        $data = new DiscountCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/discounts/{id}/edit', name: 'app_admin_discount_edit', requirements: ['id' => '\d+'])]
    public function edit(Discount $discount): Response
    {
        $data = new DiscountCrudData($discount);

        return $this->crudEdit($data);
    }

    #[Route(path: '/discounts/{id}/delete', name: 'app_admin_discount_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Discount $discount): RedirectResponse|JsonResponse
    {
        $data = new DiscountCrudData($discount);

        return $this->crudDelete($data);
    }

    #[Route(path: '/discounts/bulk/delete', name: 'app_admin_discount_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet code ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' codes ?';
    }
}


