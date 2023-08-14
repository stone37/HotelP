<?php

namespace App\Controller\Admin;

use App\Data\CrudDataInterface;
use App\Entity\User;
use App\Paginator\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\ArrayShape;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template E
 *
 * @method User|UserInterface getUser()
 */
abstract class CrudController extends AbstractController
{
    protected string $entity = '';
    protected string $templatePath = '';
    protected string $routePrefix = '';
    protected string $searchField = '';
    protected string $createFlashMessage = '';
    protected string $editFlashMessage = '';
    protected string $deleteFlashMessage = '';
    protected string $deleteMultiFlashMessage = '';
    protected string $deleteErrorFlashMessage = '';
    protected string $hybridIndexFlashMessage = '';

    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null
    ];

    public function __construct(
        protected EntityManagerInterface $em,
        protected PaginatorInterface $paginator,
        protected EventDispatcherInterface $dispatcher,
        private RequestStack $requestStack
    )
    {
    }

    public function crudIndex(QueryBuilder $query = null, FormInterface $form = null, int $pathType = null): Response
    {
        $query = $query ?: $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');

        $rows = $this->paginator->paginate($query->getQuery());

        return $this->render('admin/'. $this->templatePath .'/index.html.twig', [
            'rows' => $rows,
            'searchable' => true,
            'prefix' => $this->routePrefix,
            'searchForm' => $form ? $form->createView() : null,
            'type' => $pathType
        ]);
    }

    public function crudHybridIndex(CrudDataInterface $data): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data->hydrate();
            /** @var E $entity */
            $entity = $data->getEntity();

            $this->em->persist($entity);
            $this->em->flush();

            $this->addFlash('success', $this->hybridIndexFlashMessage);

            return $this->redirectToRoute($this->routePrefix . '_index');
        }

        return $this->render('admin/'. $this->templatePath . '/index.html.twig', [
            'form' => $form->createView(),
            'entity' => $data->getEntity()
        ]);
    }

    public function crudNew(CrudDataInterface $data): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data->hydrate();

            /** @var E $entity */
            $entity = $data->getEntity();

            $this->em->persist($entity);
            $this->em->flush();

            if ($this->events['create'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['create']($data->getEntity()));
            }

            $this->addFlash('success', $this->createFlashMessage);

            return $this->redirectToRoute($this->routePrefix . '_index');
        }

        return $this->render('admin/'. $this->templatePath . '/create.html.twig', [
            'form' => $form->createView(),
            'entity' => $data->getEntity()
        ]);
    }

    public function crudEdit(CrudDataInterface $data): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var E $entity */
            $entity = $data->getEntity();
            $old = clone $entity;
            $data->hydrate();

            $this->em->flush();

            if ($this->events['update'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['update']($entity, $old));
            }
            $this->addFlash('success', $this->editFlashMessage);

            return $this->redirectToRoute($this->routePrefix . '_index');
        }

        return $this->render('admin/'. $this->templatePath .'/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $data->getEntity()
        ]);
    }

    public function crudMove(object $entity, int $identifier = null): RedirectResponse
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has('pos')) {
            $pos = ($entity->getPosition() + (int) $request->query->get('pos'));

            if ($pos >= 0) {
                $entity->setPosition($pos);
                $this->em->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $identifier ? $this->redirectToRoute($this->routePrefix . '_index', ['id' => $identifier]) : $this->redirectToRoute($this->routePrefix . '_index') ;
    }


    public function crudDelete(CrudDataInterface $data): RedirectResponse|JsonResponse
    {
        $request = $this->requestStack->getCurrentRequest();

        $entity = $data->getEntity();
        $form = $this->deleteForm($entity);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->remove($entity);

                if ($this->events['delete'] ?? null) {
                    $this->dispatcher->dispatch(new $this->events['delete']($entity));
                }

                $this->em->flush();
                $this->addFlash('success', $this->deleteFlashMessage);
            } else {
                $this->addFlash('error', $this->deleteErrorFlashMessage);
            }

            return new RedirectResponse($request->request->get('referer'));
        }

        $render = $this->render('ui/modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $entity,
            'message' => $this->getDeleteMessage(),
            'configuration' => $this->getConfiguration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    public function crudMultiDelete(): RedirectResponse|JsonResponse
    {
        $request = $this->requestStack->getCurrentRequest();

        $ids = [];

        if ($request->query->has('data')) {
            $ids = json_decode($request->query->get('data'));
            $request->getSession()->set('data', $ids);
        }

        $form = $this->deleteMultiForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $entity = $this->getRepository()->find($id);

                    if ($this->events['delete'] ?? null) {
                        $this->dispatcher->dispatch(new $this->events['delete']($entity));
                    }

                    $this->em->remove($entity);
                }

                $this->em->flush();

                $this->addFlash('success', $this->deleteMultiFlashMessage);
            } else {
                $this->addFlash('error', $this->deleteErrorFlashMessage);
            }

            return new RedirectResponse($request->request->get('referer'));
        }

        if (count($ids) > 1) {
            $message = $this->getDeleteMultiMessage(count($ids));
        } else {
            $message = $this->getDeleteMessage();
        }

        $render = $this->render('ui/modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->getConfiguration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    public function getRepository(): EntityRepository
    {
        /** @var EntityRepository $repository */
        return $this->em->getRepository($this->entity);
    }

    protected function deleteForm(object $entity): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($this->routePrefix . '_delete', ['id' => $entity->getId()]))
            ->getForm();
    }

    protected function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($this->routePrefix . '_bulk_delete'))
            ->getForm();
    }

    public function getDeleteMessage(): string
    {
        return '';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return "$number";
    }

    #[ArrayShape(['modal' => "\string[][]"])] protected function getConfiguration(): array
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

