<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts;

interface LinkTypeHandlerGetDataInterface
{

    /**
     * Retrieves specific resource data for a menu in a form of an associative array.
     *
     * Example:
     * [resource_name] => [resource_json_packed_data]
     */
    public function getData();
}