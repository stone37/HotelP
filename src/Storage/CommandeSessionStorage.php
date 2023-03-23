<?php

namespace App\Storage;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CommandeSessionStorage
{
    public function __construct(private CommandeRepository $repository, private RequestStack $request)
    {
    }

    public function set(string $orderId): void
    {
        $this->request->getSession()->set($this->provideKey(), $orderId);
    }

    public function remove(): void
    {
        $this->request->getSession()->remove($this->provideKey());
    }

    public function getCommande(): ?Commande
    {
        if ($this->has()) {
            return $this->repository->find($this->get());
        }

        return null;
    }

    public function has(): bool
    {
        return $this->request->getSession()->has($this->provideKey());
    }

    public function get(): string
    {
        return $this->request->getSession()->get($this->provideKey());
    }

    private function provideKey(): string
    {
        return '_app_commande';
    }
}

