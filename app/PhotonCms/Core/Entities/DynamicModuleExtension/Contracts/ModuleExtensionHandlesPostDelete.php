<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support post-delete functionality.
 */
interface ModuleExtensionHandlesPostDelete
{

    /**
     * Executed after an entry has been deleted.
     *
     * @param object $item
     */
    public function postDelete($item);
}