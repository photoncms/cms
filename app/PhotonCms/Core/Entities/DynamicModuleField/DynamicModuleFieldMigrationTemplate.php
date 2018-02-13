<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleField;

use Config;
use Photon\PhotonCms\Core\Entities\Migration\MigrationTemplate;

class DynamicModuleFieldMigrationTemplate extends MigrationTemplate
{

    /**
     * Creates a new instance and sets the default path for dynamic models from photon configuration.
     */
    public function __construct()
    {
        $this->path = Config::get('photon.dynamic_model_migrations_dir');
    }
}