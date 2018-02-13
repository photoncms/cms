<?php

namespace Photon\PhotonCms\Core\Entities\Migration;

use App;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationGatewayInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassTemplate;
use Photon\PhotonCms\Core\Entities\ChangeReport\ChangeReportFactory;
use Photon\PhotonCms\Core\Entities\Migration\MigrationGateway;

class MigrationGatewayReport implements MigrationGatewayInterface
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
     * Persists the model migration class content into a migration file using the information from the model migration template.
     *
     * @param type $content
     * @param NativeClassTemplate $template
     * @throws BaseException
     */
    public function persistFromTemplate($content, NativeClassTemplate $template)
    {
        // File a change report
        $this->reportingService->fileReport(
            ChangeReportFactory::make(
                'add',
                [
                    'name' => $template->getClassName(),
                    'filename' => $template->getFileName()
                ]
            ),
            'models'
        );
        return true;
    }

    public function runFromTemplate(NativeClassTemplate $template)
    {
        // File a change report
        $this->reportingService->fileReport(
            ChangeReportFactory::make(
                'run',
                [
                    'name' => $template->getClassName(),
                    'filename' => $template->getFileName()
                ]
            ),
            'models'
        );
        return true;
    }

    public function deleteFromTemplate(NativeClassTemplate $template)
    {
        // File a change report
        $this->reportingService->fileReport(
            ChangeReportFactory::make(
                'delete',
                [
                    'name' => $template->getClassName(),
                    'filename' => $template->getFileName()
                ]
            ),
            'models'
        );
        return true;
    }
}