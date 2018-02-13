<?php

namespace Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes;

use Photon\PhotonCms\Core\Entities\FieldType\FieldType;

//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

class Date extends FieldType implements HasValidation
{
    public function getValidationString()
    {
        //ISO 8601 date 2004-02-12
        return 'nullable|date_format:Y-m-d';
    }
}