<?php

namespace Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes;

use Photon\PhotonCms\Core\Entities\FieldType\FieldType;

use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

class SystemInteger extends FieldType implements TransformsInput, TransformsOutput, HasValidation
{

    public function input($object, $attributeName, $value)
    {
        $object->$attributeName = ($value === null) ? null : (int) $value;
    }

    public function output($object, $attributeName)
    {
        return ($object->$attributeName === null) ? null : (int) $object->$attributeName;
    }

    public function getValidationString()
    {
        return 'integer';
    }
}