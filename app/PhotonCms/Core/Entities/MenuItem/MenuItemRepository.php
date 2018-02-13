<?php

namespace Photon\PhotonCms\Core\Entities\MenuItem;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\MenuItem\Contracts\MenuItemGatewayInterface;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over MenuItem entity.
 */
class MenuItemRepository
{

    /**
     * Finds a MeniItem instance by ID.
     *
     * @param int $id
     * @param MenuItemGatewayInterface $gateway
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     */
    public function find($id, MenuItemGatewayInterface $gateway)
    {
        return $gateway->retrieve($id);
    }

    /**
     * Finds a MenuItem instance by slug.
     *
     * @param string $slug
     * @param MenuItemGatewayInterface $gateway
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     */
    public function findBySlug($slug, MenuItemGatewayInterface $gateway)
    {
        return $gateway->retrieveBySlug($slug);
    }

    /**
     * Finds MenuItem instances by a menu ID.
     *
     * @param int $menuId
     * @param MenuItemGatewayInterface $gateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByMenuId($menuId, MenuItemGatewayInterface $gateway)
    {
        return $gateway->retrieveByMenuId($menuId);
    }

    public function findMenuItemAncestors($id, MenuItemGatewayInterface $gateway)
    {
        return $gateway->findMenuItemAncestors($id);
    }

    /**
     *
     * @param string|int $slugOrId
     * @param MenuItemGatewayInterface $gateway
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     */
    public function findBySlugOrId($slugOrId, MenuItemGatewayInterface $gateway)
    {
        $menuItem = $this->find($slugOrId, $gateway);

        if (!$menuItem) {
            $menuItem = $this->findBySlug($slugOrId, $gateway);
        }

        return $menuItem;
    }

    /**
     * Saves or updates a MenuItem instance from incomming data.
     *
     * @param array $data
     * @param MenuItemGatewayInterface $gateway
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     * @throws PhotonException
     */
    public function saveFromData($data, MenuItemGatewayInterface $gateway)
    {
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $menuItem = $gateway->retrieve($data['id']);

            if (is_null($menuItem)) {
                throw new PhotonException('MENU_ITEM_NOT_FOUND', ['id' => $data['id']]);
            }

            if (isset($data['parent_id']) && is_numeric($data['parent_id']) && $data['parent_id'] > 0) {
                $menuItem->parent_id = $data['parent_id'];
            }

            if (isset($data['menu_link_type_id']) && is_numeric($data['menu_link_type_id']) && $data['menu_link_type_id'] > 0) {
                $menuItem->menu_link_type_id = $data['menu_link_type_id'];
            }

            if (isset($data['title'])) {
                $menuItem->title = $data['title'];
            }

            if (isset($data['resource_data'])) {
                $menuItem->resource_data = $data['resource_data'];
            }

            if (isset($data['entry_data'])) {
                $menuItem->entry_data = $data['entry_data'];
            }

            if (isset($data['icon'])) {
                $menuItem->icon = $data['icon'];
            }

            if (isset($data['slug'])) {
                $menuItem->slug = $data['slug'];
            }

            if (isset($data['created_by']) && is_numeric($data['created_by']) && $data['created_by'] > 0) {
                $menuItem->created_by = $data['created_by'];
            }

            if (isset($data['updated_by']) && is_numeric($data['updated_by']) && $data['updated_by'] > 0) {
                $menuItem->updated_by = $data['updated_by'];
            }
        }
        else {
            $menuItem = MenuItemFactory::make($data);
        }

        if ($gateway->persist($menuItem)) {
            return $menuItem;
        }
        else {
            throw new PhotonException('MENU_ITEM_SAVE_FROM_DATA_FAILED', ['data' => $data]);
        }
    }

    /**
     * Deletes a MenuItem instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem $menuItem
     * @param MenuItemGatewayInterface $gateway
     * @return string
     */
    public function delete(MenuItem $menuItem, MenuItemGatewayInterface $gateway)
    {
        $children = $gateway->retrieveByParentId($menuItem->id);
        if(count($children) > 0) {            
            throw new PhotonException('MENU_ITEM_HAS_CHILDREN', ['menu_item' => $menuItem]);
        }

        return $gateway->delete($menuItem);
    }
}