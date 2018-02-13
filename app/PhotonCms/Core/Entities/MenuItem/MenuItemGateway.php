<?php

namespace Photon\PhotonCms\Core\Entities\MenuItem;

use Photon\PhotonCms\Core\Entities\MenuItem\Contracts\MenuItemGatewayInterface;

/**
 * Decouples repository from data sources.
 */
class MenuItemGateway implements MenuItemGatewayInterface
{

    /**
     * Retrieves MenuItem instances by a menu ID.
     *
     * @param int $menuId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveByMenuId($menuId)
    {
        return MenuItem::whereMenuId($menuId)->orderBy('lft', 'asc')->get();
    }

    /**
     * Retrieves MenuItem instances by a parent ID.
     *
     * @param int $menuId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveByParentId($parentId)
    {
        return MenuItem::whereParentId($parentId)->orderBy('lft', 'asc')->get();
    }

    public function findMenuItemAncestors($id)
    {
        $menuItem = MenuItem::find($id);

        $parent = MenuItem::find($menuItem->parent_id);

        $ancestorArray = [];
        while($parent) {
            $ancestorArray[] = $parent;
            $parent = MenuItem::find($parent->parent_id);
        }

        return $ancestorArray;
    }

    /**
     * Retrieves a MenuItem by an ID.
     *
     * @param int $id
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     */
    public function retrieve($id)
    {
        return MenuItem::find($id);
    }

    /**
     * Retrieves a MenuItem by slug.
     *
     * @param string $slug
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     */
    public function retrieveBySlug($slug)
    {
        return MenuItem::whereSlug($slug)->get()->first();
    }

    /**
     * Persists a MenuItem instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem $menuItem
     * @return boolean
     */
    public function persist(MenuItem $menuItem)
    {
        $menuItem->save();
        return true;
    }

    /**
     * Deletes a MenuItem instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem $menuItem
     * @return boolean
     */
    public function delete(MenuItem $menuItem)
    {
        return $menuItem->delete();
    }
}
