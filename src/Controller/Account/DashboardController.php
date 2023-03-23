<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Repository\BookingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class DashboardController extends AbstractController
{
    use ControllerTrait;

    private BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    #[Route(path: '/', name: 'app_user_index')]
    #[IsGranted('ROLE_USER')]
    public function index()
    {
        $user = $this->getUserOrThrow();

        return $this->render('user/dashboard/index.html.twig', [
            'user'     => $user,
            'booking' => $this->bookingRepository->getConfirmedByUserNumber($user),
        ]);
    }
}
