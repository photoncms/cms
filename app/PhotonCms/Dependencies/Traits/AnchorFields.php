<?php

namespace Photon\PhotonCms\Dependencies\Traits;

use Photon\PhotonCms\Core\Entities\Module\Module;

trait AnchorFields
{

    /**
     * Retrieves application's base url
     *
     * @return string
     */
    public function baseUrl($item, $arguments = [])
    {
        return url("/");
    }

    /**
     * Retrieves module name from ID
     *
     * @return string
     */
    public function findModule($item, $arguments = [])
    {
    	if(!isset($item->module_id)) {
    		return "";
    	}

    	$module = Module::find($item->module_id);

        return $module->name;
    }
}