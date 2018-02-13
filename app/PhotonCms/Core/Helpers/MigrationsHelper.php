<?php

namespace Photon\PhotonCms\Core\Helpers;

use Photon\PhotonCms\Core\Helpers\NumberHelper;

class MigrationsHelper
{
    public static function generateAutoMigrationClassName()
    {
        global $AUTO_MIGRATION_INCREMENT;

        if ($AUTO_MIGRATION_INCREMENT) {
            $AUTO_MIGRATION_INCREMENT++;
        }
        else {
            $AUTO_MIGRATION_INCREMENT = 1;
        }

        return 'AutoMigration'.date('U').NumberHelper::toStringWithLeadingZeros($AUTO_MIGRATION_INCREMENT, 5);
    }
}