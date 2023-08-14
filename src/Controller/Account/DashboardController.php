<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Repository\BookingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class DashboardController extends AbstractController
{
    use ControllerTrait;

    public function __construct(private BookingRepository $bookingRepository)
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/', name: 'app_user_index')]
    public function index(): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('user/dashboard/index.html.twig', [
            'user'     => $user,
            'booking' => $this->bookingRepository->getConfirmedByUserNumber($user),
        ]);
    }
}
