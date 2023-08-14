<?php

namespace App\Controller\Admin;

use App\Data\UserCrudData;
use App\Entity\User;
use App\Form\Filter\AdminUserType;
use App\Model\UserSearch;
use App\Service\UserBanService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class UserController extends CrudController
{
    protected string $entity = User::class;
    protected string $templatePath = 'user';
    protected string $routePrefix = 'app_admin_user';
    protected string $deleteFlashMessage = 'Un compte a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les comptes ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les comptes n\'a pas pu être supprimé !';

    #[Route(path: '/users', name: 'app_admin_user_index')]
    public function index(Request $request): Response
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getAdminUsers($search);

        return $this->crudIndex($query, $form, 1);
    }

    #[Route(path: '/users/no-confirm', name: 'app_admin_user_no_confirm_index')]
    public function indexN(Request $request): Response
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getUserNoConfirmed($search);

        return $this->crudIndex($query, $form, 2);
    }

    #[Route(path: '/users/deleted', name: 'app_admin_user_deleted_index')]
    public function indexD(Request $request): Response
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class);

        $form->handleRequest($request);

        $query = $this->getRepository()->getUserDeleted($search);

        return $this->crudIndex($query, $form, 3);
    }

    #[Route(path: '/users/{id}/show/{type}', name: 'app_admin_user_show', requirements: ['id' => '\d+', 'type' => '\d+'])]
    public function show(User $user, string $type): Response
    {
       return $this->render('admin/user/show.html.twig', ['user' => $user, 'type' => $type]);
    }

    #[Route(path: '/users/{id}/ban', name: 'app_admin_user_ban', requirements: ['id' => '\d+'])]
    public function ban(Request $request, UserBanService $service, User $user): RedirectResponse|JsonResponse
    {
        $service->ban($user);
        $this->getRepository()->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json([]);
        }

        $this->addFlash('success', "L'utilisateur a été banni");

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route(path: '/users/{id}/delete', name: 'app_admin_user_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(User $user): RedirectResponse|JsonResponse
    {
        $data = new UserCrudData($user);

        return $this->crudDelete($data);
    }

    #[Route(path: '/users/bulk/delete', name: 'app_admin_user_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet compte ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' comptes ?';
    }
}

