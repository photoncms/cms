<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts;

interface ModuleExtensionHasSetterExtension
{

    /**
     * Provides an ability to add extension code to the dynamic module setAll() setter.
     *
     * @param object $entry
     */
    public function executeSetterExtension($entry);
}