<?php

namespace App\Controller\Admin;

use App\Data\PromotionCrudData;
use App\Entity\Promotion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class PromotionController extends CrudController
{
    protected string $entity = Promotion::class;
    protected string $templatePath = 'promotion';
    protected string $routePrefix = 'app_admin_promotion';
    protected string $createFlashMessage = 'Une promotion a été crée';
    protected string $editFlashMessage = 'Une promotion a été mise à jour';
    protected string $deleteFlashMessage = 'Une promotion a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les promotions ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les promotions n\'a pas pu être supprimé !';

    #[Route(path: '/promotions', name: 'app_admin_promotion_index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/promotions/create', name: 'app_admin_promotion_create')]
    public function create(): RedirectResponse|Response
    {
        $entity = new Promotion();
        $data = new PromotionCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/promotions/{id}/edit', name: 'app_admin_promotion_edit', requirements: ['id' => '\d+'])]
    public function edit(Promotion $promotion): RedirectResponse|Response
    {
        $data = new PromotionCrudData($promotion);

        return $this->crudEdit($data);
    }

    #[Route(path: '/promotions/{id}/move', name: 'app_admin_promotion_move', requirements: ['id' => '\d+'])]
    public function move(Promotion $promotion): RedirectResponse
    {
        return $this->crudMove($promotion);
    }

    #[Route(path: '/promotions/show/{id}', name: 'app_admin_promotion_show', requirements: ['id' => '\d+'])]
    public function show(Promotion $promotion): Response
    {
        return $this->render('admin/promotion/show.html.twig', ['promotion' => $promotion]);
    }

    #[Route(path: '/promotions/{id}/delete', name: 'app_admin_promotion_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Promotion $promotion): RedirectResponse|JsonResponse
    {
        $data = new PromotionCrudData($promotion);

        return $this->crudDelete($data);
    }

    #[Route(path: '/promotions/bulk/delete', name: 'app_admin_promotion_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette promotion ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' promotions ?';
    }
}


