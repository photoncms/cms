<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

/**
 * This interface ensures that the extension class will have some callable extension functionalities.
 */
interface ModuleExtensionHasExtensionFunctions
{

    /**
     * Retrieves compiled extended function calls.
     *
     * @param object $item
     */
    public function getExtensionFunctionCalls($item);
}