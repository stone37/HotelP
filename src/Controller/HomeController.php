<?php

namespace App\Controller;

use App\Repository\PromotionRepository;
use App\Repository\RoomGalleryRepository;
use App\Repository\EquipmentValueRepository;
use App\Repository\GalleryRepository;
use App\Repository\RoomEquipmentRepository;
use App\Repository\RoomRepository;
use App\Service\CartService;
use App\Storage\BookingSessionStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private RoomRepository $roomRepository,
        private PromotionRepository $promotionRepository,
        private GalleryRepository $galleryRepository,
        private RoomGalleryRepository $roomGalleryRepository,
        private EquipmentValueRepository $equipmentValueRepository,
        private RoomEquipmentRepository $roomEquipmentRepository,
        private BookingSessionStorage $storage,
        private CartService $cartService
    )
    {
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(): Response
    {
        $this->storage->remove();
        $this->cartService->init();

        return $this->render('site/home/index.html.twig', [
            'galleries' => $this->galleryRepository->getGalleries(5),
            'equipments' => $this->equipmentValueRepository->findAll(),
            'roomEquipments' => $this->roomEquipmentRepository->findBy([], ['position' => 'ASC'], 10),
            'equipmentImages' => $this->galleryRepository->getGalleries(10),
            'roomEquipmentImages' => $this->roomGalleryRepository->getGalleries(10),
            'promotions' => $this->promotionRepository->findLimit(3),
            'rooms' => $this->roomRepository->getEnabled()
        ]);
    }
}
