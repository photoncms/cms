<?php

namespace Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes;

use Photon\PhotonCms\Core\Entities\FieldType\FieldType;

use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;
//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

class Permissions extends FieldType implements TransformsInput, TransformsOutput
{
    public function __construct()
    {
        $this->isAttribute = false;
        $this->isRelation = true;
        $this->requiresPivot = true;
        $this->relationType = 'ManyToMany';
    }

    public function input($object, $attributeName, $value)
    {
        if ($value === '' || !$value) {
            $value = [];
        }

        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $object->addRelationForUpdate($attributeName, $value);
    }

    public function output($object, $attributeName)
    {
        $relationName = $attributeName.'_relation';
        return $object->$relationName;
    }

// ToDo: We need a bit specific validator here. Accepted input is an array of integers, or CSV of integers. (Sasa|06/2016)
// There is an example of writing custom validators for laravel in the answer here: http://stackoverflow.com/questions/24588975/how-do-i-validate-an-array-of-integers-in-laravel
//    public function getValidationString()
//    {
//        return '';
//    }
}