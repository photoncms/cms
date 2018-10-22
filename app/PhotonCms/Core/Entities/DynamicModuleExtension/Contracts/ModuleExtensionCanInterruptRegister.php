<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

interface ModuleExtensionCanInterruptRegister
{

    /**
     * Interrupts register using dynamic module entry.
     */
    public function interruptRegister();
}