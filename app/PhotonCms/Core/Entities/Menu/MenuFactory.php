<?php

namespace Photon\PhotonCms\Core\Entities\Menu;

/**
 * Handles object manipulation.
 */
class MenuFactory
{

    /**
     * Makes an instance of a Menu.
     *
     * @param array $data
     * @return \Photon\PhotonCms\Core\Entities\Menu\Menu
     */
    public static function make($data = [])
    {
        return new Menu($data);
    }
}