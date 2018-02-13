<?php
namespace Photon\PhotonCms\Core\Exceptions;

use Config;
use Exception;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Photon\PhotonCms\Core\Transform\TransformationController;
use App;

class PhotonException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * HTTP response code
     *
     * @var int
     */
    private $statusCode;

    /**
     * An array of parameters for the response body.
     * This can be any exception related data which can help the other side determine what went wrong.
     *
     * @var array
     */
    private $bodyParameters = [];

    /**
     * Not sure yet
     *
     * @var array
     */
    private $headers = [];

    private $responseSource = '';

    /**
     *
     * @param null $responseName
     * @param array $responseData
     * @param Exception $previous
     * @param array $headers
     */
    public function __construct(
        $responseName = null,
        array $responseData = [],
        Exception $previous = null,
        array $headers = [],
        $responseSource = 'responses'
    ) {
        $this->statusCode = Config::get("$responseSource.$responseName");
        $this->message = $responseName;
        $this->bodyParameters = (!empty($responseData))
            ? $this->prepareData($responseData)
            : [];
        $this->headers = $headers;
        $this->responseSource = $responseSource;

        parent::__construct($this->message, $this->statusCode, $previous);
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns an array of response body parameters.
     *
     * @return array
     */
    public function getBodyParameters()
    {
        return $this->bodyParameters;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the name of the config file containing the response message.
     */
    public function getResponseSource()
    {
        return $this->responseSource;
    }

    /**
     * Prepares output data.
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        return App::call(
            function ($data, TransformationController $transformationController) {
                return $transformationController->transform($data);
            },
            ['data' => $data]
        );
    }
}