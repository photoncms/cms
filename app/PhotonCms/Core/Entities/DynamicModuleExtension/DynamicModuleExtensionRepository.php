<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension;

class DynamicModuleExtensionRepository
{
    public function deleteByModelName($modelName, DynamicModuleExtensionGateway $gateway)
    {
        $gateway->deleteByModelName($modelName);
    }
}