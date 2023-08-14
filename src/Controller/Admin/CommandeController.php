<?php

namespace App\Controller\Admin;

use App\Data\CommandeCrudData;
use App\Entity\Commande;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class CommandeController extends CrudController
{
    protected string $entity = Commande::class;
    protected string $templatePath = 'commande';
    protected string $routePrefix = 'app_admin_commande';
    protected string $deleteFlashMessage = 'Une commande a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les commandes ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les commandes n\'a pas pu être supprimé !';

    #[Route(path: '/commandes', name: 'app_admin_commande_index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/commandes/{id}/show', name: 'app_admin_commande_show', requirements: ['id' => '\d+'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commande/show.html.twig', ['commande' => $commande]);
    }

    #[Route(path: '/commandes/{id}/user', name: 'app_admin_commande_user', requirements: ['id' => '\d+'])]
    public function byUser(User $user): Response
    {
        $qb = $this->getRepository()->findByUser($user);

        return $this->crudIndex($qb);
    }

    #[Route(path: '/commandes/{id}/delete', name: 'app_admin_commande_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Commande $commande): RedirectResponse|JsonResponse
    {
        $data = new CommandeCrudData($commande);

        return $this->crudDelete($data);
    }

    #[Route(path: '/commandes/bulk/delete', name: 'app_admin_commande_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette commande ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' commandes ?';
    }
}


