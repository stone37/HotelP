<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Repository\PromotionRepository;
use App\Service\CartService;
use App\Storage\BookingSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class PromotionController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private EntityManagerInterface $em,
        private PromotionRepository $repository,
        private Breadcrumbs $breadcrumbs,
        private BookingSessionStorage $storage,
        private CartService $cartService,
        private PaginatorInterface $paginator)
    {
    }

    #[Route(path: '/nos-offres', name: 'app_promotion_index')]
    public function index(Request $request): Response
    {
        $this->storage->remove();
        $this->cartService->init();

        $this->breadcrumb($this->breadcrumbs)->addItem('Nos offres');

        $promotions = $this->paginator->paginate($this->repository->getTotalEnabled(), $request->query->getInt('page', 1), 15);

        return $this->render('site/promotion/index.html.twig', [
            'promotions' => $promotions
        ]);
    }

    #[Route(path: '/nos-offres/{slug}', name: 'app_promotion_show')]
    public function show(string $slug): NotFoundHttpException|Response
    {
        $promotion = $this->repository->getBySlug($slug);

        if (!$promotion) {
            return $this->createNotFoundException('Cette promotion n\'existe pas');
        }

        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Nos offres', $this->generateUrl('app_promotion_index'))
            ->addItem($promotion->getName());

        return $this->render('site/promotion/show.html.twig', [
            'promotion' => $promotion
        ]);
    }
}
