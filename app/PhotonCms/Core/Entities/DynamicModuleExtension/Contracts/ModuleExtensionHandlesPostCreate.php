<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support post-create functionality.
 */
interface ModuleExtensionHandlesPostCreate
{

    /**
     * Executed after an entry has been persisted.
     *
     * @param object $item
     * @param object $cloneAfter
     */
    public function postCreate($item, $cloneAfter);
}