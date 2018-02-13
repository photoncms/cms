<?php

namespace Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes;

use Photon\PhotonCms\Core\Entities\FieldType\FieldType;

//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

class DateTime extends FieldType implements HasValidation
{
    public function getValidationString()
    {
        //ATOM date 2004-02-12T15:19:21+00:00
        return 'nullable|date_format:Y-m-d\TH:i:sP';
    }
}