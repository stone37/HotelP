<?php

namespace App\Service;

use App\Entity\Room;
use App\Entity\RoomGallery;
use App\Manager\OrphanageManager;
use App\Repository\RoomGalleryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\File\File;

class RoomGalleryService
{
    public function __construct(
        private RequestStack $request,
        private OrphanageManager $orphanageManager,
        private UploadService $uploadService,
        private RoomGalleryRepository $repository
    )
    {
    }

    public function add(Room $room): bool
    {
        $files = $this->uploadService->getFilesUpload($this->request->getSession());

        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            $gallery = (new RoomGallery())
                ->setFile(new File($file->getPathname()));

            $this->repository->add($gallery, false);

            $room->addGallery($gallery);
        }

        $this->repository->flush();

        return true;
    }

    public function initialize(Request $request): void
    {
        if (!$request->isMethod('POST')) {
            $request->getSession()->set('app_gallery_image', []);
            $this->orphanageManager->initClear($request->getSession());
        }
    }
}

