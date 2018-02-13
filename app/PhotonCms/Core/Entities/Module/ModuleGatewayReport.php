<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use App;
use Photon\PhotonCms\Core\InstanceComparator\InstanceComparatorController;

/**
 * Decouples repository from data sources.
 */
class ModuleGatewayReport extends ModuleGateway
{

    /**
     * Instance of the reporting service.
     *
     * @var \Photon\PhotonCms\Core\Services\Reporting\ReportingService
     */
    private $reportingService;

    /**
     * Instance of instance comparator.
     *
     * @var InstanceComparatorController
     */
    private $instanceComparatorController;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->reportingService = App::make('ReportingService');
        $this->instanceComparatorController = new InstanceComparatorController();
    }

    /**
     * Persists differences into a report.
     *
     * Mocks persistance of the passed object for further survival of the script.
     *
     * @param \Photon\PhotonCms\Core\Entities\Module\Module $module
     * @return \Photon\PhotonCms\Core\Entities\Module\Module
     */
    public function persist(Module $module)
    {
        // Prepare an old object
        if ($module->exists) {
            $oldModule = $this->retrieve($module->id);
        }
        else {
            $oldModule = ModuleFactory::makeEmpty();
        }

        // Compile a difference report
        $report = App::call(
            function ($oldModule, $module, InstanceComparatorController $instanceComparatorController) {
                return $instanceComparatorController->compare($oldModule, $module);
            },
            ['oldModule' => $oldModule, 'module' => $module]
        );

        // File a report from compiled data
        $this->reportingService->fileReport($report, 'modules');

        // Mock persisted data, so the script can survive
        if (!is_numeric($module->id) || $module->id < 1) {
            $module->id = $this->getNextId();
        }

        return $module;
    }

    /**
     * Adds module deletion report to the reporting service.
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        // Compile a difference report
        $report = App::call(
            function ($oldModule, $module, InstanceComparatorController $instanceComparatorController) {
                return $instanceComparatorController->compare($oldModule, $module);
            },
            ['oldModule' => $this->retrieve($id), 'module' => ModuleFactory::makeEmpty()]
        );

        // File a report from compiled data
        $this->reportingService->fileReport($report, 'modules');

        return true;
    }
}