<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support pre-retrieve functionality.
 */
interface ModuleExtensionHandlesPreRetrieve
{

    /**
     * Executed before an entry has been persisted.
     */
    public function preRetrieve();
}