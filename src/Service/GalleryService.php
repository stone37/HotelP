<?php

namespace App\Service;

use App\Entity\Gallery;
use App\Manager\OrphanageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RequestStack;

class GalleryService
{
    public function __construct(
        private OrphanageManager $orphanageManager,
        private UploadService $uploadService,
        private EntityManagerInterface $em,
        private RequestStack $request
    )
    {
    }

    public function add(): bool
    {
        $files = $this->uploadService->getFilesUpload($this->request->getSession());

        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            $image = (new Gallery())
                ->setFile(new File($file->getPathname()));

            $this->em->persist($image);
        }

        $this->em->flush();

        return true;
    }

    public function initialize(Request $request)
    {
        if (!$request->isMethod('POST')) {
            $request->getSession()->set('app_gallery_image', []);
            $this->orphanageManager->initClear($request->getSession());
        }
    }
}

