<?php

namespace Photon\PhotonCms\Core\Helpers;

class ClassNameHelper
{

    public static function getClassNameFromNamespace($namespace)
    {
        if (stripos($namespace, ' as ')) {
            return substr($namespace, stripos($namespace, ' as ') + 4);
        }
        else {
            return self::getShortName($namespace);
        }
    }

    public static function getShortName($fullName)
    {
        $reflect = new \ReflectionClass($fullName);
        return $reflect->getShortName();
    }
}