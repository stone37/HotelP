<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Repository\CommandeRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class CommandeController extends AbstractController
{
    public function __construct(
        private CommandeRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/commandes', name: 'app_admin_commande_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $orders = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/commande/index.html.twig', ['orders' => $orders]);
    }

    #[Route(path: '/commandes/{id}/show', name: 'app_admin_commande_show', requirements: ['id' => '\d+'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commande/show.html.twig', ['order' => $commande]);
    }

    #[Route(path: '/commandes/{id}/user', name: 'app_admin_commande_user', requirements: ['id' => '\d+'])]
    public function byUser(Request $request, User $user): Response
    {
        $qb = $this->repository->findBy(['user' => $user], ['createdAt' => 'desc']);

        $orders = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/commande/index.html.twig', ['orders' => $orders]);
    }

    #[Route(path: '/commandes/{id}/delete', name: 'app_admin_commande_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Commande $commande): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($commande);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($commande);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($commande, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La commande a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la commande n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette commande ?';

        $render = $this->render('ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $commande,
            'message' => $message,
            'configuration' => $this->configuration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/commandes/bulk/delete', name: 'app_admin_commande_bulk_delete', options: ['expose' => true])]
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
                    $commande = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($commande), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($commande, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les commandes ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les commandes n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1) {
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' commandes ?';
        } else {
            $message = 'Être vous sur de vouloir supprimer cette commande ?';
        }

        $render = $this->render('ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Commande $commande): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_commande_delete', ['id' => $commande->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_commande_bulk_delete'))
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


