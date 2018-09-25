<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support post-login functionality.
 */
interface ModuleExtensionHandlesPostLogin
{

    /**
     * Executed after an entry has been persisted.
     */
    public function postLogin($item);
}