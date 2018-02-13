<?php

namespace Photon\PhotonCms\Core\Services\Logging;

use Monolog\Logger;

class LogBaseService
{
    /**
     * Type of the logger.
     *
     * @var string
     */
    protected $logType = '';

    /**
     * Available and allowed logger types.
     *
     * @var array
     */
    private static $logTypes = [
        'error'
    ];

    /**
     * Logger instance.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Sets up a logger for further usage.
     *
     * @throws PhotonException
     */
    public function __construct()
    {
        if (!in_array($this->logType, self::$logTypes)) {
            throw new PhotonException('UNDEFINED_LOG_TYPE', ['logType' => $this->logType]);
        }

        $this->logger = new Logger($this->logType);
    }
}