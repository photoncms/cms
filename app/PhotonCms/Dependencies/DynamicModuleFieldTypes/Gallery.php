<?php

namespace Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes;

use Photon\PhotonCms\Core\Entities\FieldType\FieldType;

use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;
//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

class Gallery extends FieldType implements TransformsInput, TransformsOutput
{
    public function __construct()
    {
        $this->isRelation = true;
        $this->relationType = 'ManyToOne';
    }

    public function input($object, $attributeName, $value)
    {
        $object->$attributeName = $value;
    }

    public function output($object, $attributeName)
    {
        $relationName = $attributeName.'_relation';
        return $object->$relationName;
    }

//    public function getValidationString()
//    {
//        // ToDo: We need a possibility here to compile a validation rule like 'exists:target_table,id' (Sasa|09/2016)
//    }
}