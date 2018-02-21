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

class ResponseRepository
{
    /**
     * Performs transformations.
     *
     * @var TransformationController
     */
    private $transformationController;

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
     */
    public function __construct(TransformationController $transformationController)
    {
        $this->transformationController = $transformationController;
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
            $responseSource = 'responses';
        }

        $responseCode = Config::get("$responseSource.$responseName");

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
}
