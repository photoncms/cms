<?php

namespace Photon\PhotonCms\Core\PermissionServices;

use Photon\PhotonCms\Dependencies\DynamicModels\Permissions;
use Illuminate\Support\Facades\Cache;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\PermissionServices\PermissionHelper;

class PermissionORMHelper
{

    /**
     * Apply query restrictions based on permissions
     *
     * @return null
     */
    public static function applyRestrictions(\Illuminate\Database\Eloquent\Builder $queryBuilder, $moduleTableName)
    {
        // if user is administrator grant full access
        if (PermissionHelper::isAdminUser()) {
            return $queryBuilder;
        }
        
        $user = \Auth::user();

        // if retrieve_all_entries rule exist and user has it grant full access, if rule exist and user does not have it throw exception
        $canRetrieveAll = self::canRetrieveAllEntries($moduleTableName);
        if ($canRetrieveAll === false) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['retrieve_all_entries' => $moduleTableName]);
        }

        // if retrieve_all_entries rule does not exist check which specific entry user can access
        self::applyMatchingCriteria($queryBuilder, $moduleTableName);
    }


    /**
     * Check if user can retrieve all entries
     *
     * @return boolean
     */
    private static function canRetrieveAllEntries($moduleTableName)
    {
        // get all existing permissions
        $permissions = Cache::remember('all_permissions', 60, function() {
            return Permissions::all();
        });  

        $allEntriesPermissionName = 'retrieve_all_entries:'.$moduleTableName;

        // check if retrieve_all_entries is defined and if user has it
        if ($permissions->contains('name', $allEntriesPermissionName)) {
            $user = \Auth::user();
            return $user->can($allEntriesPermissionName);
        }
    }

//    private static function canRetrieveModuleEntries($moduleTableName)
//    {
//        $allPermissions = PermissionHelper::getCurrentUserPermissions();
//
//        $RetrievePermission = "retrieve_entry_{$moduleTableName}";
//
//        if (in_array($CRUDPermission, $allPermissions)) {
//            $user = \Auth::user();
//            return $user->can($allEntriesPermissionName);
//        }
//
//        return true;
//    }

    private static function applyMatchingCriteria(\Illuminate\Database\Eloquent\Builder $queryBuilder, $moduleTableName)
    {
        $permissions = PermissionHelper::getCurrentUserPermissions(); 

        $user = \Auth::user();

        $hasMatchingPermissions = false;

        foreach ($permissions as $permission) {

            if (strpos($permission, "retrieve_module:{$moduleTableName}_match:") !== false) {
                $hasMatchingPermissions = true;
                $matchString = str_replace("retrieve_module:{$moduleTableName}", '', $permission);
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

                        $queryBuilder->whereHas("{$relation}_relation", function ($query) use ($field, $user, $userField) {
                            $query->where($field, '=', $user->$userField);
                        });
                    }
                    elseif (strpos($matchString, "_to:") !== false) {

                        $matchString = explode('_to:', $matchString);
                        $userField = $matchString[0];
                        $entryField = $matchString[1];

                        $queryBuilder->where($entryField, '=', $user->$userField);
                    }
                }
            }
        }

        return $hasMatchingPermissions;
    }
}

