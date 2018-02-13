<?php

namespace Photon\PhotonCms\Core\Entities\ModuleType;

class ModuleTypeGateway
{
    /**
     * Retrieves a ModuleType by id as a static method.
     *
     * @param int $id
     * @return ModuleType
     */
    public static function retrieveStatic($id)
    {
        return ModuleType::find($id);
    }

    /**
     * Retrieves a ModuleType by ID.
     *
     * @param int $id
     * @return ModuleType
     */
    public function retrieve($id)
    {
        return ModuleType::find($id);
    }

    /**
     * Retrieves all ModuleTypes
     *
     * @return Collection
     */
    public function retrieveAll()
    {
        return ModuleType::all();
    }
}