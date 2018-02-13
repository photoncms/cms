<?php

namespace Photon\PhotonCms\Core\Entities\MenuItem;

/**
 * Handles object manipulation.
 */
class MenuItemFactory
{

    /**
     * Makes an instance of a MenuItem.
     *
     * @param array $data
     * @return \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem
     */
    public static function make($data = [])
    {
        return new MenuItem($data);
    }
}