<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use App;
use Photon\PhotonCms\Core\InstanceComparator\InstanceComparatorController;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleFactory;

/**
 * Decouples repository from data sources.
 */
class DynamicModuleGatewayReport extends DynamicModuleGateway
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
     * Instance of a dynamic module facotry
     *
     * @var DynamicModuleFactory
     */
    private $dynamicModuleFactory;

    /**
     * Class constructor.
     */
    public function __construct($className)
    {
        $this->reportingService = App::make('ReportingService');
        $this->instanceComparatorController = new InstanceComparatorController();
        $this->dynamicModuleFactory = new DynamicModuleFactory($className);
        
        $parsedClassName = explode('\\', $className);
        $this->className = array_pop($parsedClassName);
        
        parent::__construct($className);
    }

    /**
     * Persists a single dynamic module entry into the DB.
     *
     * @param DynamicModuleInterface $entry
     * @return boolean
     */
    public function persist(DynamicModuleInterface &$entry)
    {
        // Prepare an old object
        if ($entry->exists) {
            $oldEntry = $this->retrieve($entry->id);
        }
        else {
            $oldEntry = $this->dynamicModuleFactory->makeEmpty();
        }

        // Compile a difference report
        $report = App::call(
            function ($oldEntry, $entry, InstanceComparatorController $instanceComparatorController) {
                return $instanceComparatorController->compare($oldEntry, $entry);
            },
            ['oldEntry' => $oldEntry, 'entry' => $entry]
        );

        // File a report from compiled data
        $this->reportingService->fileReport($report, $this->className);

        // Mock persisted data, so the script can survive
        if (!is_numeric($entry->id) || $entry->id < 1) {
            $entry->id = $this->getNextId();
        }

        return $entry;
    }

    /**
     * Deletes a single dynamic module entry instance by id from the DB.
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        $oldEntry = $this->retrieve($id);
        if (!$oldEntry) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $id]);
        }

        // Compile a difference report
        $report = App::call(
            function ($oldEntry, $entry, InstanceComparatorController $instanceComparatorController) {
                return $instanceComparatorController->compare($oldEntry, $entry);
            },
            ['oldEntry' => $oldEntry, 'entry' => $this->dynamicModuleFactory->makeEmpty()]
        );

        // File a report from compiled data
        $this->reportingService->fileReport($report, $this->className);

        return true;
    }
}