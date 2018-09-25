<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support post-register functionality.
 */
interface ModuleExtensionHandlesPostRegister
{

    /**
     * Executed after an entry has been persisted.
     */
    public function postRegister($item);
}