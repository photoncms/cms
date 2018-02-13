<?php

namespace Photon\PhotonCms\Core\Entities\Menu\Contracts;

use Photon\PhotonCms\Core\Entities\Menu\Menu;

interface MenuGatewayInterface
{

    /**
     * Retrieves a Menu instance by menu name.
     *
     * @param string $menuName
     * @return Photon\PhotonCms\Core\Entities\Menu\Menu
     */
    public function retrieveByName($menuName);

    /**
     * Persists a Menu instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\Menu\Menu $menu
     * @return boolean
     */
    public function persist(Menu $menu);

    /**
     * Deletes a Menu instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\Menu\Menu $menu
     * @return boolean
     */
    public function delete(Menu $menu);

    /**
     * Persists related menu link types for the specified Menu.
     *
     * @param \Photon\PhotonCms\Core\Entities\Menu\Menu $menu
     * @param array $linkTypeIds
     */
    public function persistLinkTypeIds(Menu $menu, array $linkTypeIds);
}