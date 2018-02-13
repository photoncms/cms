<?php

namespace Photon\PhotonCms\Core\Entities\Field;

use App;
use Photon\PhotonCms\Core\InstanceComparator\InstanceComparatorController;

/**
 * Decouples repository from data sources.
 */
class FieldGatewayReport extends FieldGateway
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
     * Adds field persistence report to the reporting service.
     *
     * Mocks persistance of the passed object for further survival of the script.
     *
     * @param \Photon\PhotonCms\Core\Entities\Field\Field $field
     * @return \Photon\PhotonCms\Core\Entities\Field\Field
     */
    public function persist(Field $field)
    {
        // Prepare an old object
        if ($field->exists) {
            $oldField = $this->retrieve($field->id);
        }
        else {
            $oldField = FieldFactory::makeEmpty();
        }

        // Compile a difference report
        $report = App::call(
            function ($oldField, $field, InstanceComparatorController $instanceComparatorController) {
                return $instanceComparatorController->compare($oldField, $field);
            },
            ['oldField' => $oldField, 'field' => $field]
        );

        // File a report from compiled data
        $this->reportingService->fileReport($report, 'fields');

        // Mock persisted data, so the script can survive
        if (!is_numeric($field->id) || $field->id < 1) {
            $field->id = $this->getNextId();
        }

        return $field;
    }

    /**
     * Adds field deletion report to the reporting service.
     *
     * @param \Photon\PhotonCms\Core\Entities\Field\Field $field
     * @return boolean
     */
    public function delete(Field $field)
    {
        // Compile a difference report
        $report = App::call(
            function ($oldField, $field, InstanceComparatorController $instanceComparatorController) {
                return $instanceComparatorController->compare($oldField, $field);
            },
            ['oldField' => $field, 'field' => FieldFactory::makeEmpty()]
        );

        // File a report from compiled data
        $this->reportingService->fileReport($report, 'fields');

        return true;
    }
}