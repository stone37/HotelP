<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Mailing\Mailer;
use App\Repository\BookingRepository;
use App\Repository\NewsletterDataRepository;
use App\Repository\PaymentRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private PaymentRepository $paymentRepository,
        private BookingRepository $bookingRepository,
        private RoomRepository $roomRepository,
        private NewsletterDataRepository $newsletterDataRepository
    )
    {
    }

    #[Route(path: '/', name: 'app_admin_index')]
    public function index(): Response
    {
        $taxe = $this->paymentRepository->totalTax();
        $reduction = $this->paymentRepository->totalReduction();
        $revenus = $this->paymentRepository->totalRevenues();

        $bookingConfirmNumber = $this->bookingRepository->getConfirmNumber();
        $bookingCancelNumber = $this->bookingRepository->getCancelNumber();
        $bookingArchiveNumber = $this->bookingRepository->getArchiveNumber();

        $today = new DateTime();
        $nextMonth = (new DateTime())->modify('+1 month');
        $roomTotal = $this->roomRepository->getRoomTotalNumber();
        $roomEnabledTotal = $this->roomRepository->getRoomEnabledTotalNumber();
        $roomBookingTotal = $this->bookingRepository->getRoomBookingTotalNumber($today, $nextMonth);

        $bookingTotal = $bookingConfirmNumber+$bookingCancelNumber+$bookingArchiveNumber;
        $bookingCancelPercent = ($bookingTotal > 0) ? ($bookingCancelNumber * 100) / ($bookingTotal) : 0;

        return $this->render('admin/dashboard/index.html.twig', [
            'bookingNewNumber' => $this->bookingRepository->getNewNumber(),
            'bookingConfirmNumber' => $bookingConfirmNumber,
            'bookingCancelNumber' => $bookingCancelNumber,
            'bookingArchiveNumber' => $bookingArchiveNumber,
            'bookingCancelPercent' => round($bookingCancelPercent),
            'users' => $this->userRepository->getUserNumber(),
            'lastClients' => $this->userRepository->getLastClients(),
            'lastOrders' => $this->paymentRepository->getLasts(),
            'newsletterData' => $this->newsletterDataRepository->getNumber(),
            'months' => $this->paymentRepository->getMonthlyRevenues(),
            'days' => $this->paymentRepository->getDailyRevenues(),
            'orders' => $this->paymentRepository->getNumber(),
            'revenus' => ($revenus - $taxe),
            'reduction' => $reduction,
            'roomTotal' => $roomTotal,
            'roomEnabledTotal' => $roomEnabledTotal,
            'roomBookingTotal' => $roomBookingTotal,
            'today' => $today,
            'nextMonth' => $nextMonth
        ]);
    }

    /**
     * Envoie un email de test à mail-tester pour vérifier la configuration du serveur.
     */
    #[Route(path: '/admin/mailtester', name: 'app_admin_mailtest', methods: ['POST'])]
    public function testMail(Request $request, Mailer $mailer): RedirectResponse
    {
        $email = $mailer->createEmail('mails/auth/register.twig', ['user' => $this->getUserOrThrow()])
            ->to($request->get('email'))
            ->subject('Hotel particulier | Confirmation du compte');

        $mailer->send($email);

        $this->addFlash('success', "L'email de test a bien été envoyé");

        return $this->redirectToRoute('app_admin_index');
    }

    private function getUserOrThrow(): User
    {
        $user = $this->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}

