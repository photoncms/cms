<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

interface ModuleExtensionCanInterruptRetrieve
{

    /**
     * Interrupts the retrieval of dynamic module entries.
     *
     * @param array $entries
     */
    public function interruptRetrieve(&$entries);
}