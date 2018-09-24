<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Illuminate\Http\Response;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;
use Photon\PhotonCms\Core\Entities\Field\Field;
use \Photon\PhotonCms\Core\Exceptions\PhotonException;

class FieldGroupsModuleExtensions extends BaseDynamicModuleExtension
{

    public function interruptDelete($entry)
    {
        $interrupt = parent::interruptDelete($entry);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }

        if(Field::whereFieldGroupId($entry->id)->count() > 0) {
            throw new PhotonException('FIELD_GROUP_DELETE_FAILED_FIELD_GROUP_HAS_FIELDS', ['field_group' => $entry]);
        }
    }

}