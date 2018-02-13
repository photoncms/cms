<?php

namespace Photon\PhotonCms\Core\Entities\Field\Migrations;

use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationGatewayInterface;
use Photon\PhotonCms\Core\Entities\Migration\MigrationGateway;

class FieldUpdateMigrationGateway extends MigrationGateway
{

    /**
     * Creates a migration file using a migration template.
     *
     * @param BaseMigrationTemplate $template
     * @return boolean
     */
    public function createMigrationFile(BaseMigrationTemplate $template)
    {
        if ($template instanceof PivotMigrationTemplate) {
            return Artisan::call('make:migration:pivot', $this->prepareCreationArguments($template));
        }
        else if ($template instanceof MigrationTemplate) {
            return Artisan::call('make:migration:schema', $this->prepareCreationArguments($template));
        }
    }
}