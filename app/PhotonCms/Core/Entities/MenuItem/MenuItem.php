<?php

namespace Photon\PhotonCms\Core\Entities\MenuItem;

use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeDataController;
use Photon\PhotonCms\Core\Entities\Node\ScopedNode;

use Photon\PhotonCms\Core\Entities\Node\Contracts\MaxDepthInterface;
use Photon\PhotonCms\Core\Traits\DynamicModel\ExtensionSparks;

class MenuItem extends ScopedNode implements MaxDepthInterface
{
    use ExtensionSparks;
    
    /**
     * Used for Nested Set list
     *
     * @var string
     */
    protected $scope = 'menu_id';
    protected $scoped = ['menu_id'];

    protected $fillable = ['parent_id', 'menu_id', 'menu_link_type_id', 'title', 'resource_data', 'entry_data', 'icon', 'slug', "created_by", "updated_by"];

    /**
     * Retrieves the menu maximum depth.
     *
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->menu->max_depth;
    }

    /**
     * Checks if the menu item is clickable.
     *
     * @return boolean
     */
    public function isClickable()
    {
        return $this->menu_link_type->clickable;
    }

    /**
     * Gets the menu item icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return MenuLinkTypeDataController::extractIconFromMenuItemByTypeId($this, $this->menu_link_type_id);
    }

    /**
     * Related menus.
     */
    public function menu() {
        return $this->hasOne('\Photon\PhotonCms\Core\Entities\Menu\Menu', 'id', 'menu_id');
    }

    /**
     * Related menu link types.
     */
    public function menu_link_type() {
        return $this->hasOne('\Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType', 'id', 'menu_link_type_id');
    }

    /**
     * User that created this menu item
     */
    public function created_by() {
        return $this->belongsTo('Photon\PhotonCms\Dependencies\DynamicModels\User', 'created_by');
    }

    /**
     * User that updated this menu item
     */
    public function updated_by() {
        return $this->belongsTo('Photon\PhotonCms\Dependencies\DynamicModels\User', 'updated_by');
    }

    /**
     * Gets a compiled link from the object resource data.
     *
     * @return string
     */
    public function getCompiledLink()
    {
        return MenuLinkTypeDataController::compileLinkFromDataByTypeId($this->resource_data, $this->menu_link_type_id);
    }
}