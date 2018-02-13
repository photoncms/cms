<?php

namespace Photon\PhotonCms\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Entities\Validation\ValidationTransformer;

use Illuminate\Contracts\Validation\Validator;

abstract class Request extends FormRequest
{

    /**
     * Used for generating responses.
     *
     * @var Photon\Response\ResponseRepository
     */
    private $responseRepository;

    /**
     * Used for generating responses.
     *
     * @var Photon\PhotonCms\Core\Entities\Validation\ValidationTransformer
     */
    private $validationTransformer;

    /**
     * Request constructor.
     * @param ResponseRepository $responseRepository
     */
    public function __construct(
        ResponseRepository $responseRepository,
        ValidationTransformer $validationTransformer
    )
    {
        parent::__construct();

        $this->responseRepository = $responseRepository;
        $this->validationTransformer = $validationTransformer;
    }

    /**
     * Returns an error response of a failed validation.
     *
     * @param array $errors
     * @return \Illuminate\Http\Response
     */
    public function response(array $errors)
    {
        return $this->responseRepository->make('VALIDATION_ERROR', ['error_fields' => $errors]);
    }

    /**
     * Preformat validation errors result.
     *
     * @param Validator $validator
     * @return array
     */
    protected function formatErrors(Validator $validator)
    {
        return $this->validationTransformer->transform($validator);
    }
}