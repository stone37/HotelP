<?php

namespace App\Controller\Admin;

use App\Data\PaymentCrudData;
use App\Entity\Payment;
use App\Event\PaymentRefundedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class PaymentController extends CrudController
{
    protected string $entity = Payment::class;
    protected string $templatePath = 'payment';
    protected string $routePrefix = 'app_admin_payment';
    protected string $deleteFlashMessage = 'Un paiement a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les paiements ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les paiements n\'a pas pu être supprimé !';

    #[Route(path: '/payments', name: 'app_admin_payment_index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/payments/{id}/refunded', name: 'app_admin_payment_refunded', requirements: ['id' => '\d+'])]
    public function refunded(EventDispatcherInterface $dispatcher, Payment $payment): RedirectResponse
    {
        $dispatcher->dispatch(new PaymentRefundedEvent($payment));

        $this->addFlash('success', 'Le paiement a bien été marqué comme remboursé');

        return $this->redirectToRoute('app_admin_payment_index');
    }

    #[Route(path: '/payments/report', name: 'app_admin_payment_report')]
    public function report(Request $request): Response
    {
        $year = $request->query->getInt('year', (int) date('Y'));

        return $this->render('admin/payment/report.html.twig', [
            'reports' => $this->getRepository()->getMonthlyReport($year),
            'prefix' => 'admin_transaction',
            'current_year' => date('Y'),
            'year' => $year
        ]);
    }

    #[Route(path: '/payments/{id}/delete', name: 'app_admin_payment_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Payment $payment): RedirectResponse|JsonResponse
    {
        $data = new PaymentCrudData($payment);

        return $this->crudDelete($data);
    }

    #[Route(path: '/payments/bulk/delete', name: 'app_admin_payment_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet paiement ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' paiements ?';
    }
}

