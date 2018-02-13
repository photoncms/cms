<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType\LinkTypeHandlers;

use Photon\PhotonCms\Core\Entities\MenuItem\MenuItem;

class MenuItemGroupHandler extends BaseLinkHandler
{

    protected $type = 'group';

    /**
     * Extracts an icon from a menu item.
     *
     * @param MenuItem $menuItem
     * @return string
     */
    public function extractIcon(MenuItem $menuItem)
    {
        return $menuItem->icon;
    }
}