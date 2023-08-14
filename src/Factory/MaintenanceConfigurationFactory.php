<?php

namespace App\Factory;

use App\Manager\ConfigurationFileManager;
use App\Model\MaintenanceConfiguration;

final class MaintenanceConfigurationFactory
{
    public function __construct(private ConfigurationFileManager $configurationFileManager)
    {
    }

    public function get(): MaintenanceConfiguration
    {
        $maintenanceConfiguration = new MaintenanceConfiguration();

        if (!$this->configurationFileManager->hasMaintenanceFile()) {
            return $maintenanceConfiguration;
        }

        $maintenanceConfiguration->setEnabled(true);
        return $maintenanceConfiguration->map($this->configurationFileManager->parseMaintenanceYaml());
    }
}



