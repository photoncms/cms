<?php

namespace Photon\PhotonCms\Core\Controllers;

// General
use Photon\Http\Controllers\Controller;

// Dependency injection
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Entities\ModuleType\ModuleTypeRepository;
use Photon\PhotonCms\Core\Entities\ModuleType\ModuleTypeGateway;

class ModuleTypeController extends Controller
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @var ModuleTypeRepository
     */
    private $moduleTypeRepository;

    /**
     * @var ModuleTypeGateway
     */
    private $moduleTypeGateway;

    /**
     * Controller construcor.
     *
     * @param ResponseRepository $responseRepository
     * @param ModuleTypeRepository $moduleTypeRepository
     * @param ModuleTypeGateway $moduleTypeGateway
     */
    public function __construct(
        ResponseRepository $responseRepository,
        ModuleTypeRepository $moduleTypeRepository,
        ModuleTypeGateway $moduleTypeGateway
    )
    {
        $this->responseRepository   = $responseRepository;
        $this->moduleTypeRepository = $moduleTypeRepository;
        $this->moduleTypeGateway    = $moduleTypeGateway;
    }

    /**
     * Retrieves all module types.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllModuleTypes()
    {
        $moduleTypes = $this->moduleTypeRepository->getAll($this->moduleTypeGateway);

        return $this->responseRepository->make('GET_ALL_MODULE_TYPES_SUCCESS', ['module_types' => $moduleTypes]);
    }
}