<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Model\RoomFilter;
use App\Repository\EquipmentRepository;
use App\Repository\RoomRepository;
use App\Service\BookerService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class RoomController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private RoomRepository $repository,
        private EquipmentRepository $equipmentRepository,
        private Breadcrumbs $breadcrumbs,
        private PaginatorInterface $paginator,
        private BookerService $booker
    )
    {
    }

    #[Route(path: '/hebergements', name: 'app_room_index')]
    public function index(Request $request): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Nos hébergements');

        $filter = new RoomFilter();
        $filter = $this->hydrate($request, $filter);

        $rooms = $this->paginator->paginate(
            $this->booker->roomAvailableForPeriod($this->repository->getFilter($filter)),
            $request->query->getInt('page', 1),15);

        return $this->render('site/room/index.html.twig', [
            'rooms' => $rooms
        ]);
    }

    #[Route(path: '/hebergements/{slug}', name: 'app_room_show')]
    public function show(string $slug): NotFoundHttpException|Response
    {
        $room = $this->repository->getBySlug($slug);

        if (!$room) {
            return $this->createNotFoundException('Cet hébergement n\'existe pas');
        }

        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Hébergements', $this->generateUrl('app_room_index'))
            ->addItem($room->getName());

        $equipments = $this->equipmentRepository->findAll();

        return $this->render('site/room/show.html.twig', [
            'room' => $room,
            'equipments' => $equipments
        ]);
    }

    private function hydrate(Request $request, RoomFilter $filter): RoomFilter
    {
        if ($request->query->has('adult'))
            $filter->setAdult($request->query->get('adult'));
        if ($request->query->has('children'))
            $filter->setChildren($request->query->get('children'));

        return $filter;
    }
}
