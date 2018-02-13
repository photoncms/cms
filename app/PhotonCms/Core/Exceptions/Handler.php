<?php

namespace Photon\PhotonCms\Core\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;
use App;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        ValidationException::class,
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($request->wantsJson() && !($e instanceof HttpResponseException)) {
            return $this->exceptionToJson($e);
        }

        // Default to the parent class' implementation of handler
        return parent::render($request, $e);
    }

    /**
     * Formats exception data into JSON for a response. Returns the result within a response
     *
     * @param Exception $e
     * @return \Illuminate\Http\Response
     */
    public function exceptionToJson(Exception $e)
    {
        $responseSource = ($e instanceof PhotonException) ? $e->getResponseSource() : 'responses';

        $responseName = ($e instanceof PhotonException)
            ? $e->getMessage()
            : (($e instanceof ValidationException)
                ? 'VALIDATION_ERROR'
                : 'PHP_NATIVE_EXCEPTION');

        $responseData = ($e instanceof PhotonException)
            ? $e->getBodyParameters()
            : (($e instanceof ValidationException)
                ? ['error_fields' => $e->validator]
                : [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => ((\Config::get("app.debug")) ? $e->getTrace() : 'disabled')
                ]);

        $response = App::call(
            function ($responseName, $responseData, $responseSource, ResponseRepository $responseRepository) {
                return $responseRepository->make($responseName, $responseData, $responseSource);
            },
            [
                'responseName'   => $responseName,
                'responseData'   => $responseData,
                'responseSource' => $responseSource
            ]
        );

        return $response;
    }
}
