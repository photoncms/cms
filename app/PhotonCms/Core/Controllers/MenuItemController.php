<?php

namespace Photon\PhotonCms\Core\Controllers;

use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Requests\Menu\CreateMenuItemRequest;
use Photon\PhotonCms\Core\Requests\Menu\UpdateMenuItemRequest;

use Photon\PhotonCms\Core\Entities\MenuItem\MenuItemRepository;
use Photon\PhotonCms\Core\Entities\MenuItem\Contracts\MenuItemGatewayInterface;
use Photon\PhotonCms\Core\Entities\MenuItem\MenuItemTransformer;
use Photon\PhotonCms\Core\Entities\Menu\MenuRepository;
use Photon\PhotonCms\Core\Entities\Menu\Contracts\MenuGatewayInterface;
use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeRepository;
use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\MenuLinkTypeGatewayInterface;
use Photon\PhotonCms\Core\Entities\Node\NodeRepository;

class MenuItemController extends Controller
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @var MenuItemRepository
     */
    private $menuItemRepository;

    /**
     * @var MenuItemGatewayInterface
     */
    private $menuItemGateway;

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
     * @var NodeRepository
     */
    private $nodeRepository;

    /**
     * @var MenuItemTransformer
     */
    private $menuItemTransformer;

    /**
     * Controller construcor.
     * 
     * @param ResponseRepository $responseRepository
     * @param MenuItemRepository $menuItemRepository
     * @param MenuItemGatewayInterface $menuItemGateway
     * @param MenuRepository $menuRepository
     * @param MenuGatewayInterface $menuGateway
     * @param MenuLinkTypeRepository $menuLinkTypeRepository
     * @param MenuLinkTypeGatewayInterface $menuLinkTypeGateway
     * @param NodeRepository $nodeRepository
     */
    public function __construct(
        ResponseRepository $responseRepository,
        MenuItemRepository $menuItemRepository,
        MenuItemGatewayInterface $menuItemGateway,
        MenuRepository $menuRepository,
        MenuGatewayInterface $menuGateway,
        MenuLinkTypeRepository $menuLinkTypeRepository,
        MenuLinkTypeGatewayInterface $menuLinkTypeGateway,
        NodeRepository $nodeRepository,
        MenuItemTransformer $menuItemTransformer
    )
    {
        $this->responseRepository     = $responseRepository;
        $this->menuItemRepository     = $menuItemRepository;
        $this->menuItemGateway        = $menuItemGateway;
        $this->menuRepository         = $menuRepository;
        $this->menuGateway            = $menuGateway;
        $this->menuLinkTypeRepository = $menuLinkTypeRepository;
        $this->menuLinkTypeGateway    = $menuLinkTypeGateway;
        $this->nodeRepository         = $nodeRepository;
        $this->menuItemTransformer    = $menuItemTransformer;
    }

    /**
     * Gets menu item
     *
     * @param int $itemId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getMenuItem($itemId)
    {
        $menuItem = $this->menuItemRepository->find($itemId, $this->menuItemGateway);

        if(!$menuItem) {
            throw new PhotonException('MENU_ITEM_NOT_FOUND', ['item_id' => $itemId]);
        }

        $menuItem->load(['menu', 'menu_link_type']);

        return $this->responseRepository->make('LOAD_MENU_ITEM_SUCCESS', ['menu_item' => $menuItem]);

    }

    /**
     * Gets menu items for a specified menu.
     *
     * @param string|int $menuId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getMenuItems($menuId)
    {
        // Prepare the menu ID
        $menu = $this->menuRepository->find($menuId, $this->menuGateway);

        if (!$menu) {
            throw new PhotonException('MENU_NOT_FOUND', ['menu_id' => $menuId]);
        }

        $menuItems = $this->menuItemRepository->findByMenuId($menu->id, $this->menuItemGateway);
        
        return $this->responseRepository->make('LOAD_MENU_ITEMS_SUCCESS', ['menu_items' => $menuItems]);
    }

    /**
     * Gets ancestors of specific menu item.
     *
     * @param string|int $itemId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getMenuItemAncestors($itemId)
    {
        // Prepare the menu ID
        $menuItem = $this->menuItemRepository->find($itemId, $this->menuItemGateway);

        if (!$menuItem) {
            throw new PhotonException('MENU_ITEM_NOT_FOUND', ['item_id' => $itemId]);
        }

        $menuItemAncestors = $this->menuItemRepository->findMenuItemAncestors($menuItem->id, $this->menuItemGateway);

        $result = [];
        foreach ($menuItemAncestors as $ancestor) {
            $result[] = $this->menuItemTransformer->transformForJSTreeAncestor($ancestor);
        }

        return $this->responseRepository->make('LOAD_MENU_ITEM_ANCESTORS_SUCCESS', ['ancestors' => $result]);
    }

    /**
     * Creates a menu item.
     *
     * @param CreateMenuItemRequest $request
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function createMenuItem(CreateMenuItemRequest $request)
    {
        $menuItemData = \Request::all();

        // Prepare the menu ID
        $menu = $this->menuRepository->find($menuItemData['menu_id'], $this->menuGateway);

        if(!$menu) {
            throw new PhotonException('MENU_NOT_FOUND', ['menu_id' => $menuItemData['menu_id']]);
        }

        // Check if there is a max depth limit and if it has been breached
        if (isset($menuItemData['parent_id'])) {
            $parentMenuItem = $this->menuItemRepository->find($menuItemData['parent_id'], $this->menuItemGateway);

            if ($parentMenuItem->depth == $menu->max_depth) {
                throw new PhotonException('NODE_MAX_DEPTH_REACHED', ['max' => $menu->max_depth, 'requested_depth' => ++$parentMenuItem->depth]);
            }
        }

        $menuItemData['menu_id'] = $menu->id;

        // Prepare the link menu type ID
        $menuLinkType = $this->menuLinkTypeRepository->findStatic($menuItemData['menu_link_type_id'], $this->menuLinkTypeGateway);

        if(!$menuLinkType) {
            throw new PhotonException('MENU_LINK_TYPE_NOT_FOUND', ['menu_link_type_id' => $menuItemData['menu_link_type_id']]);
        }

        if(!$menu->menu_link_types->contains($menuLinkType->id)) {
            throw new PhotonException('MENU_LINK_TYPE_NOT_ALLOWED', [
                'menu_link_type_id' => $menuItemData['menu_link_type_id'],
                'menu' => $menu
            ]);            
        }

        $menuItemData['menu_link_type_id'] = $menuLinkType->id;

        $user = \Auth::user();
        $menuItemData['created_by'] = $user->id;
        $menuItemData['updated_by'] = $user->id;

        $menuItem = $this->menuItemRepository->saveFromData($menuItemData, $this->menuItemGateway);

        return $this->responseRepository->make('CREATE_MENU_ITEM_SUCCESS', ['menu_item' => $menuItem]);
    }

    /**
     * Updates a menu item.
     *
     * @param string|int $itemId
     * @param UpdateMenuItemRequest $request
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function updateMenuItem($itemId, UpdateMenuItemRequest $request)
    {
        $menuItemData = \Request::all();

        $menuItem = $this->menuItemRepository->find($itemId, $this->menuItemGateway);

        if(!$menuItem) {
            throw new PhotonException('MENU_ITEM_NOT_FOUND', ['item_id' => $itemId]);
        }

        $menuItemData['id'] = $menuItem->id;

        // Prepare the link menu type ID
        if(isset($menuItemData['menu_link_type_id'])) {
            $menuLinkType = $this->menuLinkTypeRepository->findStatic($menuItemData['menu_link_type_id'], $this->menuLinkTypeGateway);

            if(!$menuLinkType) {
                throw new PhotonException('MENU_LINK_TYPE_NOT_FOUND', ['menu_link_type_id' => $menuItemData['menu_link_type_id']]);
            }

            $menu = $this->menuRepository->findByNameOrId($menuItem->menu_id, $this->menuGateway);
            
            if(!$menu->menu_link_types->contains($menuLinkType->id)) {
                throw new PhotonException('MENU_LINK_TYPE_NOT_ALLOWED', [
                    'menu_link_type_id' => $menuItemData['menu_link_type_id'],
                    'menu' => $menu
                ]);            
            }

            $menuItemData['menu_link_type_id'] = $menuLinkType->id;
        }
        
        $user = \Auth::user();
        $menuItemData['updated_by'] = $user->id;

        $menuItem = $this->menuItemRepository->saveFromData($menuItemData, $this->menuItemGateway);

        return $this->responseRepository->make('UPDATE_MENU_ITEM_SUCCESS', ['menu_item' => $menuItem]);
    }

    /**
     * Deletes a menu item by slug name or ID.
     *
     * @param string|int $itemId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function deleteMenuItem($itemId)
    {
        $menuItem = $this->menuItemRepository->find($itemId, $this->menuItemGateway);

        if (!$menuItem) {
            throw new PhotonException('MENU_ITEM_NOT_FOUND', ['item_id' => $itemId]);
        }

        $this->menuItemRepository->delete($menuItem, $this->menuItemGateway);

        return $this->responseRepository->make('DELETE_MENU_ITEM_SUCCESS');
    }

    /**
     * Repositions a menu item
     *
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function repositionMenuItem()
    {
        $menuId = \Request::get('menu_id');
        $action = \Request::get('action');
        $affectedItemId = \Request::get('affected_item_id');
        $targetItemId = \Request::get('target_item_id');

        // Prepare affected menu item
        $affectedNode = $this->menuItemRepository->find($affectedItemId, $this->menuItemGateway);

        if (!$affectedNode) {
            throw new PhotonException('MENU_ITEM_NOT_FOUND', ['affected_item_id' => $affectedItemId]);
        }

        // Second node placeholder
        $secondNode = null;

        // Prepare scope node
        if ($menuId && $action === 'setScope') {
            $scopeNode = $this->menuRepository->find($menuId, $this->menuGateway);

            if (!$scopeNode) {
                throw new PhotonException('MENU_NOT_FOUND', ['menu_id' => $menuId]);
            }

            $secondNode = $scopeNode;
        }

        // Prepare targeted node
        if ($targetItemId) {
            $targetNode = $this->menuItemRepository->find($targetItemId, $this->menuItemGateway);

            if (!$targetNode) {
                throw new PhotonException('MENU_ITEM_NOT_FOUND', ['target_item_id' => $targetItemId]);
            }

            $secondNode = $targetNode;
        }

        $affectedNode = $this->nodeRepository->performNodeAction($affectedNode, $action, $secondNode);

        return $this->responseRepository->make('REPOSITION_NODE_SUCCESS', ['affected_node' => $affectedNode]);
    }
}