<?php

namespace Photon\PhotonCms\Core\Services\Logging;

use Photon\PhotonCms\Core\Services\Logging\LogBaseService;
use Monolog\Logger;

// Logger handlers
use Config;
use Monolog\Handler\StreamHandler;

class ErrorLogService extends LogBaseService
{
    /**
     * Type of the logger.
     *
     * @var string
     */
    protected $logType = 'error';

    /**
     * Log service constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->logger->pushHandler(new StreamHandler(Config::get('photon.error_log'), Logger::ERROR));
    }

    /**
     * Loggs a message into the log file.
     *
     * @param string $message
     * @param mixed $data
     */
    public function log($message, $data = null)
    {
        if ($data !== null) {
            $this->logger->addError($message, $data);
        } else {
            $this->logger->addError($message);
        }
    }
}