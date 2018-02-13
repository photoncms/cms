<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support post-reposition functionality.
 */
interface ModuleExtensionHandlesPostReposition
{

    /**
     * Executed after an entry has been updated.
     *
     * @param object $item
     * @param object $cloneBefore
     * @param object $cloneAfter
     */
    public function postReposition($item, $cloneBefore, $cloneAfter);
}