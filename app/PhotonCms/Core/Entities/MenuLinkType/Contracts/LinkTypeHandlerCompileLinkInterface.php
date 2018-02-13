<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts;

interface LinkTypeHandlerCompileLinkInterface
{

    /**
     * Compiles a link from data in handlers own specific way.
     *
     * @param string $data
     */
    public function compileLinkFromData($data);
}