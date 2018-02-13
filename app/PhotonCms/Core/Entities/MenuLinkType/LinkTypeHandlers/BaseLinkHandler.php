<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType\LinkTypeHandlers;

use Photon\PhotonCms\Core\Entities\MenuItem\MenuItem;

abstract class BaseLinkHandler
{

    protected $type = 'undefined';
    protected $hasGenericIcon = false;

    /**
     * Returns the data type for usage in FE forms.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Checks if the link meny type provides a generic icon with its data.
     *
     * @return boolean
     */
    public function hasGenericIcon()
    {
        return $this->hasGenericIcon;
    }

    /**
     * Extracts an icon from the Menu Item.
     */
    abstract public function extractIcon(MenuItem $menuItem);
}