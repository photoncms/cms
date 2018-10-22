<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

interface ModuleExtensionCanInterruptLogin
{

    /**
     * Interrupts login using dynamic module entry.
     */
    public function interruptLogin();
}