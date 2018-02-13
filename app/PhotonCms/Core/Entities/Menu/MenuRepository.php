<?php

namespace Photon\PhotonCms\Core\Entities\Menu;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Menu\Contracts\MenuGatewayInterface;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over Menu entity.
 */
class MenuRepository
{

    /**
     * Finds a Menu instance by ID.
     *
     * @param int $id
     * @param MenuGatewayInterface $gateway
     * @return \Photon\PhotonCms\Core\Entities\Menu\Menu
     */
    public function find($id, MenuGatewayInterface $gateway)
    {
        return $gateway->retrieve($id);
    }

    /**
     * Finds all menus in the system.
     *
     * @param MenuGatewayInterface $gateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll(MenuGatewayInterface $gateway)
    {
        return $gateway->retrieveAll();
    }

    /**
     * Finds a menu by name.
     *
     * @param string $menuName
     * @param MenuGatewayInterface $gateway
     * @return \Photon\PhotonCms\Core\Entities\Menu\Menu
     */
    public function findByName($menuName, MenuGatewayInterface $gateway)
    {
        return $gateway->retrieveByName($menuName);
    }

    /**
     * Finds a Menu instance by an ID or name.
     *
     * @param string|int $nameOrId
     * @param MenuGatewayInterface $gateway
     * @return \Photon\PhotonCms\Core\Entities\Menu\Menu
     */
    public function findByNameOrId($nameOrId, MenuGatewayInterface $gateway)
    {
        $menu = $this->find($nameOrId, $gateway);

        if (!$menu) {
            $menu = $this->findByName($nameOrId, $gateway);
        }

        return $menu;
    }

    /**
     * Updates or creates a new entry from incomming data.
     * Update is distinguished from create by an $update flag.
     *
     * @param array $data
     * @param MenuGatewayInterface $gateway
     * @param boolean $update
     * @return \Photon\PhotonCms\Core\Entities\Menu\Menu
     * @throws PhotonException
     */
    public function saveFromData($data, MenuGatewayInterface $gateway, $update = false)
    {
        if (isset($data['name']) && $update) {
            $menu = $gateway->retrieveByName($data['name']);

            if (is_null($menu)) {
                throw new PhotonException('MENU_NOT_FOUND', ['name' => $data['name']]);
            }

            if (isset($data['title'])) {
                $menu->title = $data['title'];
            }

            if (isset($data['max_depth']) && (is_numeric($data['max_depth']) && $data['max_depth'] > 0 || $data['max_depth' === null])) {
                $menu->max_depth = $data['max_depth'];
            }

            if (isset($data['min_root']) && (is_numeric($data['min_root']) && $data['min_root'] > 0 || $data['min_root' === null])) {
                $menu->min_root = $data['min_root'];
            }

            if (isset($data['created_by']) && is_numeric($data['created_by']) && $data['created_by'] > 0) {
                $menu->created_by = $data['created_by'];
            }

            if (isset($data['updated_by']) && is_numeric($data['updated_by']) && $data['updated_by'] > 0) {
                $menu->updated_by = $data['updated_by'];
            }

            if (isset($data['description'])) {
                $menu->description = $data['description'];
            }
        }
        else {
            $menu = MenuFactory::make($data);
        }

        if ($gateway->persist($menu)) {
            if (isset($data['link_type_ids']) && is_array($data['link_type_ids'])) {
                $gateway->persistLinkTypeIds($menu, $data['link_type_ids']);
            }

            return $menu;
        }
        else {
            throw new PhotonException('MENU_SAVE_FROM_DATA_FAILED', ['data' => $data]);
        }
    }

    /**
     * Deletes a Menu instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\Menu\Menu $menu
     * @param MenuGatewayInterface $gateway
     * @return boolean
     */
    public function delete(Menu $menu, MenuGatewayInterface $gateway)
    {
        return $gateway->delete($menu);
    }
}