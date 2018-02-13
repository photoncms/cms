<?php

namespace Photon\PhotonCms\Core\Controllers;

use App;
use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Requests\Menu\CreateMenuRequest;
use Photon\PhotonCms\Core\Requests\Menu\UpdateMenuRequest;

use Photon\PhotonCms\Core\Entities\Menu\MenuRepository;
use Photon\PhotonCms\Core\Entities\Menu\Contracts\MenuGatewayInterface;
use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeRepository;
use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\MenuLinkTypeGatewayInterface;

class MenuController extends Controller
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @var MenuRepository
     */
    private $menuRepository;

    /**
     * @var MenuGatewayInterface
     */
    private $menuGateway;

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
     * @param MenuRepository $menuRepository
     * @param MenuGatewayInterface $menuGateway
     * @param MenuLinkTypeRepository $menuLinkTypeRepository
     * @param MenuLinkTypeGatewayInterface $menuLinkTypeGateway
     */
    public function __construct(
        ResponseRepository $responseRepository,
        MenuRepository $menuRepository,
        MenuGatewayInterface $menuGateway,
        MenuLinkTypeRepository $menuLinkTypeRepository,
        MenuLinkTypeGatewayInterface $menuLinkTypeGateway
    )
    {
        $this->responseRepository     = $responseRepository;
        $this->menuRepository         = $menuRepository;
        $this->menuGateway            = $menuGateway;
        $this->menuLinkTypeRepository = $menuLinkTypeRepository;
        $this->menuLinkTypeGateway    = $menuLinkTypeGateway;
    }

    /**
     * Gets a specific menu.
     *
     * @param string|int $menuId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getMenu($menuId)
    {
        $menu = $this->menuRepository->find($menuId, $this->menuGateway);

        if (!$menu) {
            throw new PhotonException('MENU_NOT_FOUND', ['menu_id' => $menuId]);
        }

        $menu->load('menu_link_types');

        return $this->responseRepository->make('LOAD_MENU_SUCCESS', ['menu' => $menu]);
    }

    /**
     * Gets all available menus in the system.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getMenus()
    {
        $menus = $this->menuRepository->findAll($this->menuGateway);
        $menus->load('menu_link_types');

        return $this->responseRepository->make('LOAD_MENUS_SUCCESS', ['menus' => $menus]);
    }

    /**
     * Creates a menu.
     *
     * @param CreateMenuRequest $request
     * @return \Illuminate\Http\Response
     */
    public function createMenu(CreateMenuRequest $request)
    {
        $data = $request->all();

        $data['link_type_ids'] = $data['menu_link_types'];

        $user = \Auth::user();
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;
        
        $menu = $this->menuRepository->saveFromData($data, $this->menuGateway);

        $menu->load('menu_link_types');

        return $this->responseRepository->make('CREATE_MENU_SUCCESS', ['menu' => $menu]);
    }

    /**
     * Updates a menu.
     *
     * @param string|int $menuId
     * @param UpdateMenuRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateMenu($menuId, UpdateMenuRequest $request)
    {
        $data = $request->all();

        $menu = $this->menuRepository->find($menuId, $this->menuGateway);
        if (!$menu) {
            throw new PhotonException('MENU_NOT_FOUND', ['menu_id' => $menuId]);
        }

        $data['name'] = $menu->name;
        if(isset($data['menu_link_types']))
            $data['link_type_ids'] = $data['menu_link_types'];

        foreach ($menu->menu_items as $menuItem) {
            if(!in_array($menuItem->menu_link_type_id, $data['link_type_ids'])) {
                throw new PhotonException('MENU_ALLOWED_LINK_TYPES_UNABLE_TO_UPDATE', [
                    'new_link_types' => $data['link_type_ids'], 
                    'menu' => $menu
                ]);
            }
        }

        $user = \Auth::user();
        $data['updated_by'] = $user->id;

        $menu = $this->menuRepository->saveFromData($data, $this->menuGateway, true);

        $menu->load('menu_link_types');

        return $this->responseRepository->make('UPDATE_MENU_SUCCESS', ['menu' => $menu]);
    }

    /**
     * Deletes a menu.
     *
     * @param string|int $menuId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function deleteMenu($menuId)
    {
        $menu = $this->menuRepository->find($menuId, $this->menuGateway);

        if (!$menu) {
            throw new PhotonException('MENU_NOT_FOUND', ['menu_id' => $menuId]);
        }

        if ($menu->is_system) {
            throw new PhotonException('DELETE_SYSTEM_MENU_FORBIDDEN', ['menu_id' => $menuId]);
        }

        $this->menuRepository->delete($menu, $this->menuGateway);

        return $this->responseRepository->make('DELETE_MENU_SUCCESS');
    }
}