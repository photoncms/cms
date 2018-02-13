<?php

namespace Photon\PhotonCms\Core\Controllers;

// General
use Photon\Http\Controllers\Controller;

// Dependency injection
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway;

class FieldTypeController extends Controller
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @var FieldTypeRepository
     */
    private $fieldTypeRepository;

    /**
     * @var FieldTypeGateway
     */
    private $fieldTypeGateway;

    /**
     * Controller construcor.
     *
     * @param ResponseRepository $responseRepository
     * @param FieldTypeRepository $fieldTypeRepository
     * @param FieldTypeGateway $fieldTypeGateway
     */
    public function __construct(
        ResponseRepository $responseRepository,
        FieldTypeRepository $fieldTypeRepository,
        FieldTypeGateway $fieldTypeGateway
    )
    {
        $this->responseRepository     = $responseRepository;
        $this->fieldTypeRepository = $fieldTypeRepository;
        $this->fieldTypeGateway    = $fieldTypeGateway;
    }

    /**
     * Retrieves all field types.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllFieldTypes()
    {
        $fieldTypes = $this->fieldTypeRepository->getAll($this->fieldTypeGateway);

        return $this->responseRepository->make('GET_ALL_FIELD_TYPES_SUCCESS', ['field_types' => $fieldTypes]);
    }
}