<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass;

use App;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassTemplateInterface;
use Photon\PhotonCms\Core\Entities\ChangeReport\ChangeReportFactory;

/**
 * Decouples repository from data sources.
 */
class NativeClassGatewayReport extends NativeClassGateway
{

    /**
     * Instance of the reporting service.
     *
     * @var \Photon\PhotonCms\Core\Services\Reporting\ReportingService
     */
    private $reportingService;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->reportingService = App::make('ReportingService');
    }

    /**
     * Adds Model persistence report to the reporting service.
     *
     * @param ModelTemplateInterface $modelTemplate
     * @return boolean
     */
    public function persist(NativeClassTemplateInterface $classTemplate)
    {
        // File a change report
        $this->reportingService->fileReport(
            ChangeReportFactory::make(
                'add',
                [
                    'name' => $classTemplate->getClassName(),
                    'filename' => $this->prepareFileName($classTemplate)
                ]
            ),
            'models'
        );
        return true;
    }

    /**
     * Adds Model deletion report to the reporting service.
     *
     * @param string $name
     * @param string $path
     * @return boolean
     */
    public function deleteClassByName($name, $path = null)
    {
        $fileName = $name.'.php';

        // File a change report
        $this->reportingService->fileReport(
            ChangeReportFactory::make(
                'delete',
                [
                    'name' => $name,
                    'filename' => $fileName
                ]
            ),
            'models'
        );
        return true;
    }
}