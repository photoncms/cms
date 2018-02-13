<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleField;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;

use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;

class FieldTransformationController
{

    /**
     * Input transformation controller.
     *
     * Transforms the input value according to the field type.
     * If the field type transformation class doesn't implement the TransformsInput interface, the value will be passed back transparently.
     *
     * @param mixed $object
     * @param string $attributeName
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    public static function input($object, $attributeName, $value, $type)
    {
        $fieldTransformer = self::getTransformerByType($type);

        if ($fieldTransformer instanceof TransformsInput) {
            $fieldTransformer->input($object, $attributeName, $value);
        }
        else {
            $object->$attributeName = $value;
        }
    }

    /**
     * Output transformation controller.
     *
     * Transforms the output value according to the field type.
     * If the field type transformation class doesn't implement the TransformsInput interface, the value will be passed back transparently.
     *
     * @param mixed $object
     * @param string $attributeName
     * @param string $type
     * @return mixed
     */
    public static function output($object, $attributeName, $type)
    {
        $fieldTransformer = self::getTransformerByType($type);

        return $fieldTransformer instanceof TransformsOutput
            ? $fieldTransformer->output($object, $attributeName)
            : $object->$attributeName;
    }

    /**
     * Retrieves a specific field type transformer instance by specified type.
     *
     * @param string $type
     * @return \Photon\PhotonCms\Core\Entities\DynamicModuleField\class
     * @throws PhotonException
     */
    private static function getTransformerByType($type)
    {
        return FieldTypeRepository::findByIdStatic($type);
    }
}