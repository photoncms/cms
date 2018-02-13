<?php

namespace Photon\PhotonCms\Core\Traits\DynamicModel;

use Photon\PhotonCms\Core\Entities\Module\ModuleLibrary;

trait MaxDepth
{

    /**
     * Retrieves the module maximum depth.
     *
     * @return int
     */
    public function getMaxDepth()
    {
        return ModuleLibrary::findByTableNameStatic($this->table)->max_depth;
    }
}