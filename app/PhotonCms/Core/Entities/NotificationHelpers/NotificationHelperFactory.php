<?php

namespace Photon\PhotonCms\Core\Entities\NotificationHelpers;

class NotificationHelperFactory
{

    public static function makeByHelperName($helperName)
    {
        $helpersNamespace = 'Photon\PhotonCms\Dependencies\Notifications\Helpers\\';
        $className = implode('',array_map('ucfirst',explode('_',$helperName)));

        $fullHelperClassName = $helpersNamespace.$className.'Helper';

        if (class_exists($fullHelperClassName)) {
            return new $fullHelperClassName();
        }
        else {
            return false;
        }
    }
}