<?php

namespace App\Controller\Admin;

use App\Entity\NewsletterData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class NewsletterController extends CrudController
{
    protected string $entity = NewsletterData::class;
    protected string $templatePath = 'newsletter';

    #[Route(path: '/emailing/newsletters', name: 'app_admin_emailing_newsletters')]
    public function index(): Response
    {
        return $this->crudIndex();
    }
}
