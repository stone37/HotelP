<?php

namespace App\Controller\Admin;

use App\Entity\Supplement;
use App\Event\AdminCRUDEvent;
use App\Form\SupplementType;
use App\Repository\SupplementRepository;
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
class SupplementController extends AbstractController
{
    public function __construct(
        private SupplementRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/supplements', name: 'app_admin_supplement_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['position' => 'ASC']);

        $supplements = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/supplement/index.html.twig', [
            'supplements' => $supplements
        ]);
    }

    #[Route(path: '/supplements/create', name: 'app_admin_supplement_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        $supplement = new Supplement();

        $form = $this->createForm(SupplementType::class, $supplement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($supplement);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($supplement, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un supplément a été crée');

            return $this->redirectToRoute('app_admin_supplement_index');
        }

        return $this->render('admin/supplement/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/supplements/{id}/edit', name: 'app_admin_supplement_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Supplement $supplement): RedirectResponse|Response
    {
        $form = $this->createForm(SupplementType::class, $supplement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($supplement);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un supplément a été mise à jour');

            return $this->redirectToRoute('app_admin_supplement_index');
        }

        return $this->render('admin/supplement/edit.html.twig', [
            'form' => $form->createView(),
            'supplement' => $supplement,
        ]);
    }

    #[Route(path: '/supplements/{id}/move', name: 'app_admin_supplement_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, Supplement $supplement): RedirectResponse
    {
        if ($request->query->has('pos')) {
            $pos = ($supplement->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $supplement->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_supplement_index');
    }

    #[Route(path: '/supplements/{id}/delete', name: 'app_admin_supplement_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Supplement $supplement): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($supplement);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($supplement);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($supplement, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le supplément a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, supplément n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cet supplément ?';

        $render = $this->render('ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $supplement,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/supplements/bulk/delete', name: 'app_admin_supplement_bulk_delete', options: ['expose' => true])]
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
                    $supplement = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($supplement), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($supplement, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les suppléments ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les suppléments n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1) {
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' suppléments ?';
        } else {
            $message = 'Être vous sur de vouloir supprimer cet suppléments ?';
        }

        $render = $this->render('ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Supplement $supplement): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_supplement_delete', ['id' => $supplement->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_supplement_bulk_delete'))
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
                ],
            ]
        ];
    }
}


