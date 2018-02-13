<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support pre-delete functionality.
 */
interface ModuleExtensionHandlesPreDelete
{

    /**
     * Executed before an entry has been deleted.
     *
     * @param object $item
     * @param object $cloneBefore
     */
    public function preDelete($item, $cloneBefore);
}