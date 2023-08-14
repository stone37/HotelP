<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AddClassActiveExtension extends AbstractExtension
{
    public function __construct(private RequestStack $request)
    {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('isActive', array($this, 'check'))];
    }

    public function check($routesToCheck): bool
    {
        $currentRoute = $this->request->getMainRequest()->get('_route');
        
        if ($routesToCheck == $currentRoute) {
            return true;
        }

        return false;
    }
}