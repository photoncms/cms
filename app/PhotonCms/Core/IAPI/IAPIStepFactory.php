<?php

namespace Photon\PhotonCms\Core\IAPI;

class IAPIStepFactory
{
    /**
     * Makes an instance of IAPIFunctionStep or IAPIAttributeStep depending on input arguments.
     *
     * @param string $name
     * @param array|null $arguments
     * @return \Photon\PhotonCms\Core\IAPI\IAPIFunctionStep|\Photon\PhotonCms\Core\IAPI\IAPIAttributeStep
     */
    public static function make($name, $arguments = null)
    {
        if ($arguments === null) {
            return new IAPIAttributeStep($name);
        }
        else {
            return new IAPIFunctionStep($name, $arguments);
        }
    }
}