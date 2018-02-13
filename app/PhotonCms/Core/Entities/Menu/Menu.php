<?php

namespace Photon\PhotonCms\Core\Entities\Menu;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    protected $fillable = ['name', 'title', 'max_depth', 'min_root', 'is_system', 'description', "created_by", "updated_by"];

    /**
     * Related menu items
     */
    public function menu_items() {
        return $this->hasMany('Photon\PhotonCms\Core\Entities\MenuItem\MenuItem', 'menu_id');
    }

    /**
     * Related menu link types.
     */
    public function menu_link_types() {
        return $this->belongsToMany('Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType', 'menu_link_types_menus', 'menu_id', 'menu_link_type_id');
    }

    /**
     * User that created this menu
     */
    public function created_by() {
        return $this->belongsTo('Photon\PhotonCms\Dependencies\DynamicModels\User', 'created_by');
    }

    /**
     * User that updated this menu
     */
    public function updated_by() {
        return $this->belongsTo('Photon\PhotonCms\Dependencies\DynamicModels\User', 'updated_by');
    }
}