<?php

namespace Photon\PhotonCms\Core\Entities\ModuleType;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class ModuleTypeRepository
{
    
    public function getAll(ModuleTypeGateway $moduleTypeGateway)
    {
        return $moduleTypeGateway->retrieveAll();
    }

    /**
     * Finds a module type by ID.
     *
     * @param int $id
     * @param \Photon\PhotonCms\Core\Entities\ModuleType\ModuleTypeGateway $moduleTypeGateway
     * @return ModuleType
     * @throws PhotonException
     */
    public function findById($id, ModuleTypeGateway $moduleTypeGateway)
    {
        return $moduleTypeGateway->retrieve($id);
    }

    /**
     * Preloads all ModuleType entries into the ORM.
     * This prevents multiple querying during runtime.
     *
     * @param \Photon\PhotonCms\Core\Entities\ModuleType\ModuleTypeGateway $moduleTypeGateway
     */
    public function preloadAll(ModuleTypeGateway $moduleTypeGateway)
    {
        $moduleTypeGateway->retrieveAll();
    }
}