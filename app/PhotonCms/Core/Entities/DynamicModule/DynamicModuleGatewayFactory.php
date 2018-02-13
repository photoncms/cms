<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use App;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleGateway;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleGatewayReport;

/**
 * Handles object manipulation.
 */
class DynamicModuleGatewayFactory
{
    /**
     * Makes a dynamic module gateway class or report class depending if the reporting service is on.
     *
     * @param string $className
     * @param string $tableName
     * @return DynamicModuleGatewayReport|DynamicModuleGateway
     */
    public static function make($className, $tableName = null)
    {
        $reportingService = App::make('ReportingService');

        if ($reportingService->isActive()) {
            return new DynamicModuleGatewayReport($className);
        }
        else {
            return new DynamicModuleGateway($className, $tableName);
        }
    }
}