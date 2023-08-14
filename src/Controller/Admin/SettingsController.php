<?php

namespace App\Controller\Admin;

use App\Data\SettingsCrudData;
use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class SettingsController extends CrudController
{
    protected string $entity = Settings::class;
    protected string $templatePath = 'settings';
    protected string $routePrefix = 'app_admin_settings';
    protected string $hybridIndexFlashMessage = 'Les paramètres du site ont été mise à jour';

    #[Route(path: '/settings', name: 'app_admin_settings_index')]
    public function index(SettingsRepository $repository): Response
    {
        $settings = $repository->getSettings();

        if (null === $settings) {
            $settings = new Settings();
        }

        $data = new SettingsCrudData($settings);

        return $this->crudHybridIndex($data);
    }
}

