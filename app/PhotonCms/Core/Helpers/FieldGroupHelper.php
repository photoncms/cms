<?php

namespace Photon\PhotonCms\Core\Helpers;

use Photon\PhotonCms\Dependencies\DynamicModels\FieldGroups;

class FieldGroupHelper
{

    /**
     * Check if field group is valid
     *
     * @param \Photon\PhotonCms\Core\Entities\Module\Module $module 
     * @param int $fieldGroupId 
     * @return boolean
     */
    public static function validateFieldGroup($module, $fieldGroupId)
    {
        $group = FieldGroups::find($fieldGroupId);

        if(!$group) {
            return false;
        }

        if($module->id != $group->module_id) {
            return false;
        }
        
        return true;
    }
}