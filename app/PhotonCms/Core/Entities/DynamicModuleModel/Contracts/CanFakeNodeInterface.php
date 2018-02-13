<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleModel\Contracts;

/**
 * This interface is used only to point out that instances of a class implementing it can be used to fake Node instances.
 */
interface CanFakeNodeInterface
{

    public function getAll();
}