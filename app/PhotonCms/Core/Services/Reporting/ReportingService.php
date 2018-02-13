<?php

namespace Photon\PhotonCms\Core\Services\Reporting;

use Photon\PhotonCms\Core\Entities\ChangeReport\Contracts\ChangeReportInterface;

/**
 * Globally reports any changes which will be made (persisted) in any way inside the system, during a request execution.
 * Service must be activated by passing a parameter 'reporting=true', otherwise it will be ignored.
 */
class ReportingService
{
    /**
     * Service activity state.
     *
     * @var boolean
     */
    private $on = false;

    /**
     * Service report stack which contains information for all changes which would be made on the system.
     *
     * @var array
     */
    private $reportStack = [];

    /**
     * Activates the service.
     */
    public function activate()
    {
        $this->on = true;
    }

    /**
     * Checks if the service is active.
     *
     * This should be used in binding interface in the app service provider,
     * to determine should a regular gateway instance be provided, or should
     * an instance of a reporting gateway be provided.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->on;
    }

    /**
     * Files a report into the report stack of the service.
     *
     * @param ChangeReportInterface $report
     * @param string $stackName
     */
    public function fileReport(ChangeReportInterface $report, $stackName = '')
    {
        if ($stackName === '') {
            $this->reportStack[] = $report->toArray();
        }
        else {
            $this->reportStack[$stackName][] = $report->toArray();
        }
    }

    /**
     * Returns the service report stack.
     *
     * @return array
     */
    public function getReport()
    {
        return $this->reportStack;
    }
}