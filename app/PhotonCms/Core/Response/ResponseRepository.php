<?php
/**
 * @author Sasa Andjelic
 */

namespace Photon\PhotonCms\Core\Response;

use App;
use Config;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Transform\TransformationController;
use Illuminate\Http\Response;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\Field\FieldRepository;
use Photon\PhotonCms\Core\Entities\Field\Contracts\FieldGatewayInterface;

class ResponseRepository
{
    /**
     * Performs transformations.
     *
     * @var TransformationController
     */
    private $transformationController;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var ModuleGatewayInterface
     */
    private $moduleGateway;

    /**
     * @var FieldTypeRepository
     */
    private $fieldTypeRepository;

    /**
     * @var FieldTypeGateway
     */
    private $fieldTypeGateway;

    /**
     * Service for reporting changes
     *
     * @var ReportingService
     */
    private $reportingService;

    /**
     * Constructor.
     *
     * @param TransformationController $transformationController
     * @param ModuleRepository $moduleRepository
     * @param ModuleGatewayInterface $moduleGateway
     * @param FieldRepository $fieldRepository
     * @param FieldGatewayInterface $fieldGateway
     */
    public function __construct(
        TransformationController $transformationController,
        ModuleRepository $moduleRepository,
        ModuleGatewayInterface $moduleGateway,
        FieldRepository $fieldRepository,
        FieldGatewayInterface $fieldGateway
    )
    {
        $this->transformationController = $transformationController;
        $this->moduleRepository = $moduleRepository;
        $this->moduleGateway    = $moduleGateway;
        $this->fieldRepository         = $fieldRepository;
        $this->fieldGateway            = $fieldGateway;
        $this->reportingService = App::make('ReportingService');
    }

    /**
     * Returns a prepared API Response instance with prepared data.
     *
     * @param string $responseName
     * @param array $responseData
     * @return \Illuminate\Http\Response
     */
    public function make($responseName, $responseData = [], $responseSource = null)
    {
        if (!$responseSource) {
            $data = include base_path("app/PhotonCms/Core/Config/responses.php");
            $responseCode = $data[$responseName];
        } else {
            $responseCode = Config::get("$responseSource.$responseName");
        }

        if (!$responseCode) {
            throw new PhotonException('UNDEFINED_RESPONSE_CODE', ['name' => $responseName]);
        }

        $content = [
            'message' => $responseName,
            'body' => ($this->reportingService->isActive() && $responseCode < 300)
                ? $this->reportingService->getReport()
                : ((!empty($responseData))
                    ? $this->transformationController->transform($responseData)
                    : [])
        ];

        $this->logResponse($content);

        $this->trimResponse($content);

        return new Response($content, $responseCode);
    }

    private function logResponse($content)
    {
        if(!env("PHOTON_STORE_LOGS", true))
            return true;

        $user = \Auth::user();

        $logData = [
            "request_url" => url()->current(),
            "request_method" => \Request::method(),
            "request_data" => \Request::all(),
            "response_data"  => $content,
            "user" => $user ? $user->id . " - " . $user->first_name . " " . $user->last_name : null
        ];

        $orderLog = new Logger("api");
        $rotatingHandler = new RotatingFileHandler(storage_path('logs/photon/api.log'), env("PHOTON_MAX_DAILY_LOGS", 30), Logger::INFO);
        $orderLog->pushHandler($rotatingHandler);
        $orderLog->addInfo('photon', $logData);
    }

    /**
     * Trim transformed generic object based on include paramether.
     *
     * @param void
     */
    private function trimResponse(array &$array)
    {
        // if not retreiving dynamic module entry return
        if(!isset($array['body']['entries']) && !isset($array['body']['entry'])) {
            return;
        }

        // if there are no filtered fields return
        $includedFields = \Request::get('include');
        if(!$includedFields) {
            return;
        }

        $includedFields = explode(",", $includedFields);

        // $this->validateIncludedFields($includedFields);

        // trim single entry
        if(isset($array['body']['entry'])) {
            foreach ($array['body']['entry'] as $key => $value) {
                if(!in_array($key, $includedFields)) {
                    unset($array['body']['entry'][$key]);
                }
            }

            return;
        }

        foreach ($array['body']['entries'] as $entryKey => $entry) {
            foreach ($entry as $key => $value) {
                if(!in_array($key, $includedFields)) {
                    unset($array['body']['entries'][$entryKey][$key]);
                }
            }
        }
        return;
    }

    private function validateIncludedFields($includedFields)
    {
        $tableName = \Request::route('tableName');
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);
        $fields = $this->fieldRepository->findByModuleId($module->id, $this->fieldGateway);
        // $fields->map(function($item){ 
        //     return array('column_name' => $item->column_name, 'relation_name' => $item->relation_name)
        // });
        dd($fields->only(['id']) );
    }
}
