<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts;

use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType;

interface MenuLinkTypeGatewayInterface
{

    /**
     * Retrieves all available MenuLinkType instances.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveAll();

    /**
     * Retrieves MenuLinkType instances by an array of menu link type names.
     *
     * @param array $linkTypeNames
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveByNames(array $linkTypeNames);

    /**
     * Retrieves a MenuLinkType instance by a name.
     *
     * @param string $linkTypeName
     * @return \Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType
     */
    public function retrieveByName($linkTypeName);

    /**
     * Retrieves a MenuLinkType instance by ID.
     *
     * @param int $linkTypeId
     * @return \Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType
     */
    public static function retrieveStatic($linkTypeId);
}