<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

interface ModuleExtensionCanInterruptCreate
{

    /**
     * Interrupts creation of a dynamic module entry.
     */
    public function interruptCreate();
}