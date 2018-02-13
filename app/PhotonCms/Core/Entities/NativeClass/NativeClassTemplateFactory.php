<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass;

use Config;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Handles object manipulation.
 */
class NativeClassTemplateFactory
{
    /**
     * Makes an instance of a ModelTemplate.
     *
     * @return \Photon\PhotonCms\Core\Entities\NativeClass\NativeClassTemplate
     */
    public function make()
    {
        return new NativeClassTemplate();
    }
}