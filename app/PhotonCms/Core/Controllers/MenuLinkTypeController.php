<?php

namespace Photon\PhotonCms\Core\Controllers;

use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeRepository;
use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\MenuLinkTypeGatewayInterface;

use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeDataController;

class MenuLinkTypeController extends Controller
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @var MenuLinkTypeRepository
     */
    private $menuLinkTypeRepository;

    /**
     * @var MenuLinkTypeGatewayInterface
     */
    private $menuLinkTypeGateway;

    /**
     * Controller construcor.
     *
     * @param ResponseRepository $responseRepository
     * @param MenuLinkTypeRepository $menuLinkTypeRepository
     * @param MenuLinkTypeGatewayInterface $menuLinkTypeGateway
     */
    public function __construct(
        ResponseRepository $responseRepository,
        MenuLinkTypeRepository $menuLinkTypeRepository,
        MenuLinkTypeGatewayInterface $menuLinkTypeGateway
    )
    {
        $this->responseRepository     = $responseRepository;
        $this->menuLinkTypeRepository = $menuLinkTypeRepository;
        $this->menuLinkTypeGateway    = $menuLinkTypeGateway;
    }

    /**
     * Gets all available menu link types.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMenuLinkTypes()
    {
        $menuLinkTypes = $this->menuLinkTypeRepository->getAll($this->menuLinkTypeGateway);

        return $this->responseRepository->make('LOAD_MENU_LINK_TYPES_SUCCESS', ['menu_link_types' => $menuLinkTypes]);
    }

    /**
     * Gets menu link type resources.
     *
     * @param string $typeName
     * @return \Illuminate\Http\Response
     */
    public function getMenuLinkTypeResources($typeName)
    {
        $menuLinkResourceCollection = MenuLinkTypeDataController::getDataByTypeName($typeName);

        return $this->responseRepository->make('LOAD_MENU_LINK_TYPE_RESOURCES_SUCCESS', $menuLinkResourceCollection);
    }
}