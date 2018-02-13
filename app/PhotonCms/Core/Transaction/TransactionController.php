<?php

namespace Photon\PhotonCms\Core\Transaction;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use App;

class TransactionController
{
    /**
     * Name of the transaction controller.
     *
     * @var string
     */
    private $name = '';

    /**
     * Array of transaction sets of functions.
     * One array entry contains a following dataset:
     * - function name : name of a function which can be used for handling of the error.
     * - function forward functionality : actual necessary functionality that needs to be performed.
     * - function rollback functionality : rollback functionality which will be executed if the transaction fails, and if the forward functionality was successful.
     * - flag which determines if failure of this function should cause transaction to roll back.
     * [
     *      'function' => [anonymous function],
     *      'functionName' => [function name],
     *      'rollback' => [anonymous function],
     *      'rollbackName' => [function name],
     *      'critical' => [boolean]
     * ]
     *
     * @var array
     */
    private $queue = [];

    /**
     * Array of rollback functions which need to be executed if the transaction fails.
     * One array entry contains a following dataset:
     * - function name : name of a failed function.
     * - rollback functionality : functionality which needs to be executed to rollback.
     * [
     *      'name' => [function name],
     *      'function' => [anonymous function]
     * ]
     *
     * @var array
     */
    private $rollbacks = [];

    /**
     * An array of failed function names.
     * Can be used for handling after transaction fails.
     *
     * @var array
     */
    private $failedFunctions = [];

    /**
     * Array of possible failed rollbacks.
     * Can be used for handling, and should be used in debugging to determine what failed to rollback.
     * Some functionalities cannot be rolled back, so a user needs to be notified what are the consequences.
     *
     * @var array
     */
    private $failedRollbacks = [];

    /**
     * Service for reporting system changes invoked by a request.
     *
     * @var ReportingService
     */
    private $reportingService;

    /**
     * Creates a new instance of transaction controller.
     *
     * @param string $name
     */
    public function __construct($name = '') {
        $this->name = $name;
        $this->exceptionHandler = App::make('Illuminate\Contracts\Debug\ExceptionHandler');
        $this->errorLogger = App::make('ErrorLogService');
        $this->reportingService = App::make('ReportingService');
    }

    /**
     * Adds a functionality to the transaction queue.
     *
     * @param function  $function               Function which will be executd
     * @param function  $rollback [optional]    Function which will be executed if transaction fails
     * @param string    $functionName [optional]Function name in some kind of system notation for later dynamic handling
     * @param string    $rollbackName [optional]Rollback name in some kind of system notation for later dynamic handling
     * @param boolean   $critical [optional]    Flag which determines if failure of this function should cause transaction to roll back
     * 
     * @throws TransactionException
     */
    public function queue($function, $rollback = null, $functionName = '', $rollbackName = '', $critical = true) {
        if (!is_callable($function)) {
            throw new PhotonException('TRANSACTION_FAILURE_ILLEGAL_QUEUE_FUNCTION', ['functionName' => $functionName]);
        }
        if (!is_callable($rollback) && $rollback !== null) {
            throw new PhotonException('TRANSACTION_FAILURE_ILLEGAL_QUEUE_ROLLBACK', ['rollbackName' => $functionName]);
        }

        $this->queue[] = [
            'function' => $function,
            'functionName' => $functionName,
            'rollback' => $rollback,
            'rollbackName' => $rollbackName,
            'critical' => true
        ];
    }

    /**
     * Adds a rollback function to the transaction.
     *
     * @param function $function
     * @param string $name [optional]
     *
     * @throws TransactionException
     */
    public function addRollBack($function, $name = '') {
        if (!is_callable($function)) {
            throw new PhotonException('TRANSACTION_FAILURE_ILLEGAL_ROLLBACK', ['rollbackName' => $name]);
        }

        $this->rollbacks[] = [
            'name' => $name,
            'function' => $function
        ];
    }

    /**
     * Starts the transaction
     */
    public function commit() {
        foreach ($this->queue as $function) {
            try {
                $function['function']();
            } catch (\Exception $exception) {
                $this->functionFailed($function['functionName'], $exception);
                if ($function['critical']) {
                    $this->failAndRollBack();
                }
            }
            if ($function['rollback'] !== null) {
                $this->addRollBack($function['rollback'], $function['rollbackName']);
            }
        }
    }

    /**
     * Stops the transaction and rolls back all currently completed functionalities.
     *
     * If the transaction has been named at creation, failure log will be added into error log file.
     *
     * @throws TransactionException
     */
    public function failAndRollBack() {
        // Roll everything back if the reporting service isn't active
        if (!$this->reportingService->isActive()) {
            foreach ($this->rollbacks as $rollback) {
                try {
                    $rollback['function']();
                } catch (\Exception $exception) {
                    $this->rollbackFailed($rollback['name'], $exception);
                }
            }
        }

        // Log everything
        if ($this->name !== '') {
            $this->errorLogger->log('TRANSACTION:'.$this->name, $this->compileReport());
        }

        // Throw final exception
        throw new PhotonException(
            'TRANSACTION_FAILURE_ROLLED_BACK',
            [
                'failed_functions' => $this->failedFunctions,
                'failed_rollbacks' => $this->failedRollbacks
            ]
        );
    }

    /**
     * Registers that a function failed.
     *
     * @param string $name
     */
    private function functionFailed($name, $exception = null) {
        $this->failedFunctions[$name] = $this->exceptionHandler->exceptionToJson($exception)->getOriginalContent();
    }

    /**
     * Registers that a rollback failed.
     *
     * @param string $name
     */
    private function rollbackFailed($name, $exception = null) {
        $this->failedFunctions[$name] = $this->exceptionHandler->exceptionToJson($exception)->getOriginalContent();
    }

    /**
     * Returns an array of names of failed functions, rollbacks and expected functions
     * [
     *      'failed_functions' => [],
     *      'failed_rollbacks' => [],
     *      'expected_functions' => []
     * ]
     *
     * @return array
     */
    private function compileReport() {
        $report = [
            'failed_functions' => $this->failedFunctions,
            'failed_rollbacks' => $this->failedRollbacks
        ];

        foreach ($this->queue as $function) {
            $report['expected_functions'][] = $function['functionName'];
        }

        return $report;
    }
}