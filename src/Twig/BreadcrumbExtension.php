<?php

namespace App\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbExtension extends AbstractExtension
{
    public function __construct(private Breadcrumbs $breadcrumbs, private RouterInterface $router)
    {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('breadcrumb', array($this, 'addBreadcrumb'))];
    }

    public function addBreadcrumb($label, $url = '', array $translationParameters = array())
    {
        if (!$this->breadcrumbs->count()) {
            $this->breadcrumbs->addItem('Accueil', $this->router->generate('app_home'));
        }

        $this->breadcrumbs->addItem($label, $url, $translationParameters);
    }
}