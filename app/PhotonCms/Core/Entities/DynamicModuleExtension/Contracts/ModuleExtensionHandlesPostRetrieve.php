<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support post-retrieve functionality.
 */
interface ModuleExtensionHandlesPostRetrieve
{

    /**
     * Executed after an entry has been persisted.
     */
    public function postRetrieve($item);
}