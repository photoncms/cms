<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

interface ModuleExtensionHasGetterExtension
{

    /**
     * Provides an ability to add extension code to the dynamic module getAll() getter.
     *
     * @param object $entry
     */
    public function executeGetterExtension($entry);
}