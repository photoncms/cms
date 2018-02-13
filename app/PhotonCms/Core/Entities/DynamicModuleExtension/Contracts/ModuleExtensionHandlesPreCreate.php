<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will with support pre-create functionality.
 */
interface ModuleExtensionHandlesPreCreate
{

    /**
     * Executed before an entry has been persisted.
     *
     * @param object $item
     * @param object $cloneAfter
     */
    public function preCreate($item, $cloneAfter);
}