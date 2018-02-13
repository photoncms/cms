<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support pre-update functionality.
 */
interface ModuleExtensionHandlesPreUpdate
{

    /**
     * Executed before an entry has been updated.
     *
     * @param object $item
     * @param object $cloneBefore
     * @param object $cloneAfter
     */
    public function preUpdate($item, $cloneBefore, $cloneAfter);
}