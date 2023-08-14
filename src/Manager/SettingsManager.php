<?php

namespace App\Manager;

use App\Repository\SettingsRepository;

class SettingsManager
{
    public function __construct(private SettingsRepository $repository)
    {
    }

    public function get()
    {
        return $this->repository->getSettings();
    }
}

