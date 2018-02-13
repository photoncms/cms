<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support pre-reposition functionality.
 */
interface ModuleExtensionHandlesPreReposition
{

    /**
     * Executed before an entry has been updated.
     *
     * @param object $item
     * @param object $cloneBefore
     */
    public function preReposition($item, $cloneBefore);
}