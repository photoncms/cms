<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

interface ModuleExtensionCanInterruptUpdate
{

    /**
     * Interrupts update of a dynamic module entry.
     *
     * @param object $entry
     */
    public function interruptUpdate($entry);
}