<?php

namespace Photon\PhotonCms\Core\Entities\Menu;

use Photon\PhotonCms\Core\Entities\Menu\Contracts\MenuGatewayInterface;

/**
 * Decouples repository from data sources.
 */
class MenuGateway implements MenuGatewayInterface
{

    /**
     * Retrieves a Menu instance by ID.
     *
     * @param int $id
     * @return \Photon\PhotonCms\Core\Entities\Menu\Menu
     */
    public function retrieve($id)
    {
        return Menu::find($id);
    }

    /**
     * Retrieves all menus from the system.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveAll()
    {
        return Menu::all();
    }

    /**
     * Retrieves a Menu instance by menu name.
     *
     * @param string $menuName
     * @return Photon\PhotonCms\Core\Entities\Menu\Menu
     */
    public function retrieveByName($menuName)
    {
        return Menu::whereName($menuName)->get()->first();
    }

    /**
     * Persists a Menu instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\Menu\Menu $menu
     * @return boolean
     */
    public function persist(Menu $menu)
    {
        $menu->save();
        return true;
    }

    /**
     * Deletes a Menu instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\Menu\Menu $menu
     * @return boolean
     */
    public function delete(Menu $menu)
    {
        return $menu->delete();
    }

    /**
     * Persists related menu link types for the specified Menu.
     *
     * @param \Photon\PhotonCms\Core\Entities\Menu\Menu $menu
     * @param array $linkTypeIds
     */
    public function persistLinkTypeIds(Menu $menu, array $linkTypeIds)
    {
        $menu->menu_link_types()->detach();
        $menu->menu_link_types()->attach($linkTypeIds);
    }
}