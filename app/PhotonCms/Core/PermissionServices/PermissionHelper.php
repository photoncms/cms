<?php

namespace Photon\PhotonCms\Core\PermissionServices;

use Photon\PhotonCms\Dependencies\DynamicModels\Permissions;
use Illuminate\Support\Facades\Cache;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class PermissionHelper
{
    /**
     * Retrieves permissions of a current user.
     *
     * @return type
     */
    public static function getCurrentUserPermissions()
    {
        $user = \Auth::user();

        $assignedPermissions = [];
        foreach ($user->permissions as $permission) {
            $assignedPermissions[] = $permission->name;
        }
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                if (!in_array($permission->name, $assignedPermissions)) {
                    $assignedPermissions[] = $permission->name;
                }
            }
        }
        return $assignedPermissions;
    }

    /**
     * Checks if the current user is an administrator.
     *
     * @return boolean
     */
    public static function isAdminUser()
    {
        return \Auth::user()->hasRole('super_administrator');
    }
}

