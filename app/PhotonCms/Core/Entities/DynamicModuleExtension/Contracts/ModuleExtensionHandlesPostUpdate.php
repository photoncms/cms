<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support post-update functionality.
 */
interface ModuleExtensionHandlesPostUpdate
{

    /**
     * Executed after an entry has been updated.
     *
     * @param object $item
     * @param object $cloneBefore
     * @param object $cloneAfter
     */
    public function postUpdate($item, $cloneBefore, $cloneAfter);
}