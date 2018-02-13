<?php

namespace Photon\PhotonCms\Core\Entities\FieldType;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class FieldTypeFactory
{
    /**
     * Makes a FieldType object from a supplied base object.
     *
     * @param \Photon\PhotonCms\Core\Entities\FieldType\FieldType $baseObject
     * @return \Photon\PhotonCms\Core\Entities\FieldType\class
     * @throws PhotonException
     */
    public static function makeFromBaseObject(FieldType $baseObject)
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $baseObject->type)));

        $class = \Config::get('photon.dynamic_module_field_types_dir') . $className;

        if (!class_exists($class)) {
            throw new PhotonException('CLASS_DOESNT_EXIST', ['class' => $class]);
        }

        $typeObject = new $class();
        $typeObject->id = $baseObject->id;
        $typeObject->type = $baseObject->type;
        $typeObject->laravel_type = $baseObject->laravel_type;
        $typeObject->is_system = $baseObject->is_system;

        return $typeObject;
    }

    public static function makeFromBaseArray($baseObject)
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $baseObject['type'])));

        $class = \Config::get('photon.dynamic_module_field_types_dir') . $className;

        if (!class_exists($class)) {
            throw new PhotonException('CLASS_DOESNT_EXIST', ['class' => $class]);
        }

        $typeObject = new $class();
        $typeObject->id = $baseObject['id'];
        $typeObject->type = $baseObject['type'];
        $typeObject->laravel_type = $baseObject['laravel_type'];
        $typeObject->is_system = $baseObject['is_system'];

        return $typeObject;
    }

    public static function makeNewByClassName($className)
    {
        $class = \Config::get('photon.dynamic_module_field_types_dir') . $className;

        if (!class_exists($class)) {
            throw new PhotonException('CLASS_DOESNT_EXIST', ['class' => $class]);
        }

        $typeObject = new $class();

        return $typeObject;
    }
}