<?php

namespace Photon\PhotonCms\Core\Entities\MenuItem\Contracts;

use Photon\PhotonCms\Core\Entities\MenuItem\MenuItem;

interface MenuItemGatewayInterface
{

    /**
     * Retrieves MenuItem instances by a menu ID.
     *
     * @param int $menuId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveByMenuId($menuId);

    /**
     * Retrieves a MenuItem by an ID.
     *
     * @param int $id
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     */
    public function retrieve($id);

    /**
     * Retrieves a MenuItem by slug.
     *
     * @param string $slug
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     */
    public function retrieveBySlug($slug);

    /**
     * Persists a MenuItem instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem $menuItem
     * @return boolean
     */
    public function persist(MenuItem $menuItem);

    /**
     * Deletes a MenuItem instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem $menuItem
     * @return boolean
     */
    public function delete(MenuItem $menuItem);
}