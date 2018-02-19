<?php

namespace Photon\PhotonCms\Dependencies\Traits;

trait AnchorFields
{

    /**
     * Retrieves application's base url
     *
     * @return string
     */
    public function baseUrl($item, $arguments = [])
    {
        return url("/");
    }
}