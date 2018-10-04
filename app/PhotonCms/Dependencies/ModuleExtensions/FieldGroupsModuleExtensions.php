<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Illuminate\Http\Response;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostDelete;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostUpdate;
use Photon\PhotonCms\Core\Entities\Field\Field;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Helpers\DatabaseHelper;

class FieldGroupsModuleExtensions extends BaseDynamicModuleExtension implements
    ModuleExtensionHandlesPostCreate,
    ModuleExtensionHandlesPostUpdate,
    ModuleExtensionHandlesPostDelete
{

    /*****************************************************************
     * These functions represent interrupters for regular dynamic module entry flow.
     * If an instance of \Illuminate\Http\Response is returned, the rest of the flow after it will be interrupted.
     */
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

    /*****************************************************************
     * These functions represent the event handlers for create/update/delete actions over a dynamic module model.
     * Their return is not handled, so returning responses from here is useless.
     * Each function can be interrupted by throwing an exception. Throwing an exception will also stop the whole process. For
     * example, if a preCreate function throws an exception, this means, since the object hasn't been saved yet, the object
     * will never be saved at all.
     */
    public function postCreate($item, $cloneAfter)
    {
        DatabaseHelper::rebuildSeeders();
    }

    public function postUpdate($item, $cloneBefore, $cloneAfter)
    {
        DatabaseHelper::rebuildSeeders();
    }

    public function postDelete($item)
    {
        DatabaseHelper::rebuildSeeders();
    }

}