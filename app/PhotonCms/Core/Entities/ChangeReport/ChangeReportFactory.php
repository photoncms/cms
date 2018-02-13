<?php

namespace Photon\PhotonCms\Core\Entities\ChangeReport;

use Photon\PhotonCms\Core\Entities\ChangeReport\ChangeReport;

/**
 * Handles object manipulation.
 */
class ChangeReportFactory
{
    /**
     *  Makes a ChangeReport instance.
     *
     * @param string $changeType
     * @param array $data
     * @return ChangeReport
     */
    public static function make($changeType = null, $data = null)
    {
        return new ChangeReport($changeType, $data);
    }
}