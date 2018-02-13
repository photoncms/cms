<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule\Contracts;

interface DynamicModuleInterface
{
    /**
     * Setts all attributes of dynamic module entry object which match its attribute names.
     *
     * @param array $data
     */
    public function setAll(array &$data);

    /**
     * Retrieves all values of a dynamic module entry instance as a compiled array.
     *
     * @return array
     */
    public function getAll();
}