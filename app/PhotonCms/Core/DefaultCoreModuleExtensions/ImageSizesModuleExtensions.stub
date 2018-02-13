<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;

class ImageSizesModuleExtensions extends BaseDynamicModuleExtension implements
	ModuleExtensionHandlesPreCreate
{
    public function preCreate($item, $cloneAfter)
    {
        $data = [];
        $data['active'] = 0;
        
        $item->setAll($data);
    }

}