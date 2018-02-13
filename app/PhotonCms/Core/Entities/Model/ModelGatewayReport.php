<?php

namespace Photon\PhotonCms\Core\Entities\Model;

use App;
use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface;
use Photon\PhotonCms\Core\Entities\ChangeReport\ChangeReportFactory;

/**
 * Decouples repository from data sources.
 */
class ModelGatewayReport extends ModelGateway
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
    public function persistFromTemplate($content, ModelTemplateInterface $template)
    {
        // File a change report
        $this->reportingService->fileReport(
            ChangeReportFactory::make(
                'add',
                [
                    'name' => $template->getModelName(),
                    'filename' => $this->prepareFileName($template)
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
    public function deleteClassByName($name)
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