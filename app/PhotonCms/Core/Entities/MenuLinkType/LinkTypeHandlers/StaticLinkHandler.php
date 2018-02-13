<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType\LinkTypeHandlers;

use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\LinkTypeHandlerCompileLinkInterface;

use Photon\PhotonCms\Core\Entities\MenuItem\MenuItem;

class StaticLinkHandler extends BaseLinkHandler implements LinkTypeHandlerCompileLinkInterface
{

    protected $type = 'text';

    /**
     * Compiles a link from the provided data for this menu link type.
     *
     * @param string $data
     * @return string
     */
    public function compileLinkFromData($data)
    {
        return $data;
    }

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