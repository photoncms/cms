<?php

namespace Photon\PhotonCms\Core\Entities\Module\Contracts;

use Photon\PhotonCms\Core\Entities\Module\Module;

interface ModuleGatewayInterface
{

    /**
     * Persists a Module instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\Module\Module $module
     * @return boolean
     */
    public function persist(Module $module);

    /**
     * Deletes a Module instance by ID.
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}