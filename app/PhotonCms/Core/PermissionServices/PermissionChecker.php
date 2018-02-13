<?php

namespace Photon\PhotonCms\Core\PermissionServices;

use Photon\PhotonCms\Dependencies\DynamicModels\Roles;
use Photon\PhotonCms\Dependencies\DynamicModels\Permissions;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Illuminate\Support\Facades\Cache;
use Photon\PhotonCms\Core\PermissionServices\PermissionHelper;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;

class PermissionChecker
{

    /**
     * Check if the current user can retrieve a specific role.
     *
     * @param string $role
     * @return boolean
     */
    public static function canRetrieveRole($role)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }

        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $roleRetrievalPermissionName = 'retrieve_role:'.$role;

        if ($permissions->contains('name', $roleRetrievalPermissionName)) {
            $user = \Auth::user();
            return $user->can($roleRetrievalPermissionName);
        }

        return true;
    }
    
    /**
     * Check if the current user can assign a specific role.
     *
     * @param string $role
     * @return boolean
     */
    public static function canAssignRole($role)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }
        
        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $roleAssignmentPermissionName = 'assign_role:'.$role;

        if ($permissions->contains('name', $roleAssignmentPermissionName)) {
            $user = \Auth::user();
            return $user->can($roleAssignmentPermissionName);
        }

        return true;
    }

    /**
     * Check if the current user can revoke a specific role.
     *
     * @param string $role
     * @return boolean
     */
    public static function canRevokeRole($role)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }

        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $roleAssignmentPermissionName = 'revoke_role:'.$role;

        if ($permissions->contains('name', $roleAssignmentPermissionName)) {
            $user = \Auth::user();
            return $user->can($roleAssignmentPermissionName);
        }

        return true;
    }

    /**
     * Check if the current user can modify a specific module.
     *
     * @param string $module
     * @return boolean
     */
    public static function canModifyModule($module)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }
        
        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $moduleModifyPermissonName = 'modify_module:'.$module;

        if ($permissions->contains('name', $moduleModifyPermissonName)) {
            $user = \Auth::user();
            return $user->can($moduleModifyPermissonName);
        }

        return true;
    }

    /**
     * Check if a permission to retrieve all module entries exists.
     *
     * @param string $module
     * @return boolean
     */
    public static function hasRetrieveAllModuleEntries($module)
    {
        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $allEntriesPermissionName = 'retrieve_all_entries:'.$module;

        if ($permissions->contains('name', $allEntriesPermissionName)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the current user can retrieve all module entries.
     *
     * @param string $module
     * @return boolean
     */
    public static function canRetrieveAllModuleEntries($module)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }
        
        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $allEntriesPermissionName = 'retrieve_all_entries:'.$module;

        if ($permissions->contains('name', $allEntriesPermissionName)) {
            $user = \Auth::user();
            return $user->can($allEntriesPermissionName);
        }

        return true;
    }

    /**
     * Check if the current user can create a matching module entry.
     *
     * @param string $module
     * @param array  $data
     * @return boolean
     */
    public static function canCRUDCreateMatchingRequestData($module, $data)
    {
        // if user is admin he can create it
        if (PermissionHelper::isAdminUser()) {
            return true;
        }
        
        $permissions = PermissionHelper::getCurrentUserPermissions();
        $user = \Auth::user();

        foreach ($permissions as $permission) {
            if (strpos($permission, "create_module:{$module}_match:") !== false) {
                $matchString = str_replace("create_module:{$module}", '', $permission);
                $matchStrings = explode('_match:', $matchString);
                foreach ($matchStrings as $matchString) {
                    if($matchString == "")
                        continue;
                    // for relations -> retrieve_module:itineraries_match:id_in:crew_field:id
                    if (strpos($matchString, "_in:") !== false) {
                        // id_in:crew_field:id
                        $matchString = explode('_in:', $matchString);
                        $userField = $matchString[0];

                        // crew_field:id
                        if (strpos($matchString[1], "_field:") !== false) {
                            $matchString = explode('_field:', $matchString[1]);
                            $relation = $matchString[0];
                            $field = $matchString[1];
                        }
                        // if we did not define the field, default is id
                        else {
                            $relation = $matchString[1];
                            $field = 'id';
                        }
                        
                        // if relation value is not sent false
                        if(!isset($data[$relation]))
                            return false;

                        // find array of related ids
                        if(is_array($data[$relation]))
                            $arrayOfIds = $data[$relation];
                        if(is_int($data[$relation]))
                            $arrayOfIds = [$data[$relation]];
                        if(is_string($data[$relation]))
                            $arrayOfIds = explode(",", $data[$relation]);

                        // if field is id check from array of ids
                        if($field == 'id') {
                            if(!in_array($user->{$userField}, $arrayOfIds))
                                return false;
                            else 
                                return true;
                        }

                        $module = ModuleRepository::findByTableNameStatic($module);
                        $module->load("fields");

                        // if field is not `id` search from related module
                        foreach ($module->fields as $moduleField) {
                            if($moduleField->relation_name == $relation) {
                                $relatedModule = ModuleRepository::findByIdStatic($moduleField->related_module);
                                continue;
                            }
                        }

                        $count = \DB::table($relatedModule->table_name)
                            ->whereIn('id', $arrayOfIds)
                            ->where($field, "=", $user->{$userField})
                            ->count();

                        if(!$count)
                            return false;
                    }
                    else {
                        $matchString = explode('_to:', $matchString);

                        if(!isset($data[$matchString[1]]))
                            return false;
                        if (count($matchString) == 2 && $user->{$matchString[0]} != $data[$matchString[1]])
                            return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Check if the current user can retrieve a matching module entry.
     *
     * @param string $module
     * @param string $entry
     * @return boolean
     */
    public static function canCRUDRetrieveMatchingModuleEntry($module, $entry)
    {
        return self::canCRUDMatchingModuleEntry('retrieve', $module, $entry);
    }

    /**
     * Check if the current user can update a matching module entry.
     *
     * @param string $module
     * @param string $entry
     * @return boolean
     */
    public static function canCRUDUpdateMatchingModuleEntry($module, $entry)
    {
        return self::canCRUDMatchingModuleEntry('update', $module, $entry);
    }

    /**
     * Check if the current user can delete a matching module entry.
     *
     * @param string $module
     * @param string $entry
     * @return boolean
     */
    public static function canCRUDDeleteMatchingModuleEntry($module, $entry)
    {
        return self::canCRUDMatchingModuleEntry('delete', $module, $entry);
    }

    /**
     * Check if the current user can perform a specific CRUD action over a matching module entry.
     *
     * @param type $action
     * @param type $module
     * @param type $entry
     * @return boolean
     */
    public static function canCRUDMatchingModuleEntry($action, $module, $entry)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }
        
        $permissions = PermissionHelper::getCurrentUserPermissions();
        $user = \Auth::user();

        foreach ($permissions as $permission) {
            if (strpos($permission, "{$action}_module:{$module}_match:") !== false) {
                $matchString = str_replace("{$action}_module:{$module}", '', $permission);
                $matchStrings = explode('_match:', $matchString);
                foreach ($matchStrings as $matchString) {
                    // for relations -> retrieve_module:itineraries_match:id_in:crew_field:id
                    if (strpos($matchString, "_in:") !== false) {
                        // id_in:crew_field:id
                        $matchString = explode('_in:', $matchString);
                        $userField = $matchString[0];

                        // crew_field:id
                        if (strpos($matchString[1], "_field:") !== false) {
                            $matchString = explode('_field:', $matchString[1]);
                            $relation = $matchString[0];
                            $field = $matchString[1];
                        }
                        // if we did not define the field, default is id
                        else {
                            $relation = $matchString[1];
                            $field = 'id';
                        }
                        
                        if (
                            $entry->{$relation.'_relation'}
                        ) {
                            // for many to many and many to one
                            if ($entry->{$relation.'_relation'} instanceof \Illuminate\Support\Collection) {
                                $match = false;
                                foreach ($entry->{$relation.'_relation'} as $relatedItem) {
                                    if ($user->$userField == $relatedItem->$field) {
                                        $match = true;
                                    }
                                }
                                if (!$match) {
                                    return false;
                                }
                            }
                            // just in case to cover one to many if someone matches other field than id
                            else {
                                if ($user->$userField !== $entry->{$relation.'_relation'}->$field) {
                                    return false;
                                }
                            }
                        }
                    }
                    else {
                        $matchString = explode('_to:', $matchString);
                        if (count($matchString) == 2 && $user->{$matchString[0]} !== $entry->{$matchString[1]}) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Check if the current user can edit a specific module field.
     *
     * @param string $module
     * @param string $field
     * @return boolean
     */
    public static function canEditModuleField($module, $field)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }

        $permissions = PermissionHelper::getCurrentUserPermissions();

        $fieldEditPermissionName = "cannot_edit_field:{$module}:{$field}";

        if (in_array($fieldEditPermissionName, $permissions)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the current user can create an entry within a specific module.
     *
     * @param string $moduleTableName
     * @return boolean
     */
    public static function canCRUDCreate($moduleTableName)
    {
        return self::canCRUD('create', $moduleTableName);
    }

    /**
     * Check if the current user can update an entry within a specific module.
     *
     * @param string $moduleTableName
     * @return boolean
     */
    public static function canCRUDUpdate($moduleTableName)
    {
        return self::canCRUD('update', $moduleTableName);
    }

    /**
     * Check if the current user can delete an entry within a specific module.
     *
     * @param string $moduleTableName
     * @return boolean
     */
    public static function canCRUDDelete($moduleTableName)
    {
        return self::canCRUD('delete', $moduleTableName);
    }

    /**
     * Check if the current user can perform a CRUD action over a specific module.
     *
     * @param string $action
     * @param string $moduleTableName
     * @return boolean
     */
    private static function canCRUD($action, $moduleTableName)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }
        
        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $CRUDPermission = "{$action}_entry:{$moduleTableName}";

        if ($permissions->contains('name', $CRUDPermission)) {
            $user = \Auth::user();
            return $user->can($CRUDPermission);
        }

        return true;
    }

    /**
     * Check if there is a create permission to create entries for a specific module.
     *
     * @param string $moduleTableName
     * @return boolean
     */
    public static function hasCRUDCreatePermission($moduleTableName)
    {
        return self::hasCRUD('create', $moduleTableName);
    }

    /**
     * Check if there is a update permission to create entries for a specific module.
     *
     * @param string $moduleTableName
     * @return boolean
     */
    public static function hasCRUDUpdatePermission($moduleTableName)
    {
        return self::hasCRUD('update', $moduleTableName);
    }

    /**
     * Check if there is a delete permission to create entries for a specific module.
     *
     * @param string $moduleTableName
     * @return boolean
     */
    public static function hasCRUDDeletePermission($moduleTableName)
    {
        return self::hasCRUD('delete', $moduleTableName);
    }

    /**
     * Check if there is a permission to perform a specific CRUD action over a specific module.
     *
     * @param string $action
     * @param string $moduleTableName
     * @return boolean
     */
    private static function hasCRUD($action, $moduleTableName)
    {
        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $CRUDPermission = "{$action}_entry:{$moduleTableName}";

        if ($permissions->contains('name', $CRUDPermission)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the current user can update a specific module entry.
     *
     * @param string $module
     * @param string $entry
     * @return boolean
     */
    public static function canCRUDUpdateSpecific($module, $entry)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }

        if (self::canCRUDUpdate($module)) {
            if (!self::canCRUDUpdateMatchingModuleEntry($module, $entry)) {
                return false;
            }
        }
        else {
            return false;
        }
        return true;
    }

    /**
     * Check if the current user can delete a specific module entry.
     *
     * @param type $module
     * @param type $entry
     * @return boolean
     */
    public static function canCRUDDeleteSpecific($module, $entry)
    {
        if (PermissionHelper::isAdminUser()) {
            return true;
        }

        if (self::canCRUDDelete($module)) {
            if (!self::canCRUDDeleteMatchingModuleEntry($module, $entry)) {
                return false;
            }
        }
        else {
            return false;
        }
        return true;
    }
}