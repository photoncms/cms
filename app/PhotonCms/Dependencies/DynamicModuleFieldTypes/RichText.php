<?php

namespace Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes;

use Photon\PhotonCms\Core\Entities\FieldType\FieldType;

use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;
//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

class RichText extends FieldType implements TransformsInput, TransformsOutput
{
    
    public function input($object, $attributeName, $value)
    {
        $object->$attributeName = $value;
    }

    public function output($object, $attributeName)
    {
        return $object->$attributeName;
    }
}