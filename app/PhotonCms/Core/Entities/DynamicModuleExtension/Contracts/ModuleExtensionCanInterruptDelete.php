<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

interface ModuleExtensionCanInterruptDelete
{

    /**
     * Interrupts deletion of a dynamic module entry.
     *
     * @param object $entry
     */
    public function interruptDelete($entry);
}