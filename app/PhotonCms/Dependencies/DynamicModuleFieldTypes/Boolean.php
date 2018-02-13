<?php

namespace Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes;

use Photon\PhotonCms\Core\Entities\FieldType\FieldType;

use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

class Boolean extends FieldType implements TransformsInput, TransformsOutput, HasValidation
{

    public function input($object, $attributeName, $value)
    {
        $object->$attributeName = in_array($value, [true, 1, "1"], true);
    }

    public function output($object, $attributeName)
    {
        return (bool) $object->$attributeName;
    }

    public function getValidationString()
    {
        return 'boolean';
    }
}