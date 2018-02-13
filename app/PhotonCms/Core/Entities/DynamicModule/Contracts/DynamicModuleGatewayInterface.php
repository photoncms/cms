<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule\Contracts;

use Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface;

interface DynamicModuleGatewayInterface
{

    /**
     * Persists a single dynamic module entry into the DB.
     *
     * @param DynamicModuleInterface $entry
     * @return boolean
     */
    public function persist(DynamicModuleInterface &$entry);

    /**
     * Deletes a single dynamic module entry instance by id from the DB.
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}