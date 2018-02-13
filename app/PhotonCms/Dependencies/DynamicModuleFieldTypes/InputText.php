<?php

namespace Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes;

use Photon\PhotonCms\Core\Entities\FieldType\FieldType;

//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsInput;
//use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\TransformsOutput;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

class InputText extends FieldType implements HasValidation
{
    public function getValidationString()
    {
        // allows letters, numbers, spaces and the following characters ,.?;:'"-()!/@$
//        return 'Regex:/^[A-Za-z0-9_\-! ,\?\$\'\"\/@\.:;\(\)]+$/';

        return 'nullable|string';
    }
}