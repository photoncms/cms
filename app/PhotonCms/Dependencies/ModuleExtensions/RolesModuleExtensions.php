<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Illuminate\Http\Response;
// Enable/disable usage of these interfaces according to your needs.
// Functions promissed by an interface will never be called if the interface which promisses them isn't implemented for the class.
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;
use Photon\PhotonCms\Core\PermissionServices\PermissionChecker;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostUpdate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostDelete;
use Illuminate\Support\Facades\Cache;

/**
 * This is an example of the dynamic module extension class. This is the second layer controller of the HMVC architecture.
 * Walk through comments in this file to get familiar with each section.
 */
class RolesModuleExtensions extends BaseDynamicModuleExtension implements
    ModuleExtensionHandlesPostCreate,
    ModuleExtensionHandlesPostUpdate,
    ModuleExtensionHandlesPostDelete
{

    /*****************************************************************
     * These functions represent interrupters for regular dynamic module entry flow.
     * If an instance of \Illuminate\Http\Response is returned, the rest of the flow after it will be interrupted.
     */
    public function interruptCreate()
    {
        $interrupt = parent::interruptCreate();
        if ($interrupt instanceof Response) {
            return $interrupt;
        }

        \Auth::user()->forgetCachedPermissions();
    }

    public function interruptRetrieve(&$entries)
    {
        if (!PermissionChecker::hasRetrieveAllModuleEntries($this->tableName) || !PermissionChecker::canRetrieveAllModuleEntries($this->tableName)) {
            foreach ($entries as $key => $entry) {
                if (
                    !PermissionChecker::canCRUDRetrieveMatchingModuleEntry($this->tableName, $entry) ||
                    !PermissionChecker::canRetrieveRole($entry->name)
                ) {
                    unset($entries[$key]);
                }
            }
        }
        // Resets the underlying laravel collection array keys in case some elements have been removed
        if ($entries instanceof \Illuminate\Support\Collection) {
            $entries = $entries->values();
        }
    }

    public function interruptUpdate($entry)
    {
        $interrupt = parent::interruptUpdate($entry);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }

        \Auth::user()->forgetCachedPermissions();
    }

    public function interruptDelete($entryId)
    {
        $interrupt = parent::interruptDelete($entryId);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }

        \Auth::user()->forgetCachedPermissions();
    }

    public function postCreate($item, $cloneAfter)
    {
        if(config("photon.use_photon_cache")) {
            Cache::tags(env("APPLICATION_URL"))->flush();
        }
    }

    public function postUpdate($item, $cloneBefore, $cloneAfter)
    {
        if(config("photon.use_photon_cache")) {
            Cache::tags(env("APPLICATION_URL"))->flush();
        }
    }

    public function postDelete($item)
    {
        if(config("photon.use_photon_cache")) {
            Cache::tags(env("APPLICATION_URL"))->flush();
        }
    }
}