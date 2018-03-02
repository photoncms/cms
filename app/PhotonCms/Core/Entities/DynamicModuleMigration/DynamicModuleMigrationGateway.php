<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleMigration;

use Photon\PhotonCms\Core\Entities\Migration\MigrationGateway;
use Illuminate\Support\Facades\Artisan;

class DynamicModuleMigrationGateway extends MigrationGateway
{

    public static function runModelMigrations()
    {
        $path = config('photon.dynamic_model_migrations_dir');

        return Artisan::call('migrate', ['--path' => $path, '--force' => true]);
    }
}