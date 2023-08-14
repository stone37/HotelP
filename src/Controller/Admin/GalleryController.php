<?php

namespace App\Controller\Admin;

use App\Data\GalleryCrudData;
use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Service\GalleryService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class GalleryController extends CrudController
{
    protected string $entity = Gallery::class;
    protected string $templatePath = 'gallery';
    protected string $routePrefix = 'app_admin_gallery';
    protected string $deleteFlashMessage = 'Une image a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les images ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les images n\'a pas pu être supprimée !';

    #[Route(path: '/galleries', name: 'app_admin_gallery_index', options: ['expose' => true])]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/galleries/add', name: 'app_admin_gallery_add')]
    public function add(Request $request, GalleryService $service): RedirectResponse|Response
    {
        $service->initialize($request);

        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $value = $service->add();

            if ($value) {
                $this->addFlash('success', 'Image(s) ajouter à la galerie');
            } else {
                $this->addFlash('info', 'Aucune image sélectionner');
            }

            return $this->redirectToRoute('app_admin_gallery_index');
        }

        return $this->render('admin/gallery/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/galleries/{id}/move', name: 'app_admin_gallery_move', requirements: ['id' => '\d+'])]
    public function move(Gallery $gallery): RedirectResponse
    {
        return $this->crudMove($gallery);
    }

    #[Route(path: '/galleries/{id}/delete', name: 'app_admin_gallery_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Gallery $gallery): RedirectResponse|JsonResponse
    {
        $data = new GalleryCrudData($gallery);

        return $this->crudDelete($data);
    }

    #[Route(path: '/galleries/bulk/delete', name: 'app_admin_gallery_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette image ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' images ?';
    }
}
