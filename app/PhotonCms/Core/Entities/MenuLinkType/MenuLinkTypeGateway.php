<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType;

use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\MenuLinkTypeGatewayInterface;

/**
 * Decouples repository from data sources.
 */
class MenuLinkTypeGateway implements MenuLinkTypeGatewayInterface
{

    /**
     * Retrieves all available MenuLinkType instances.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveAll()
    {
        return MenuLinkType::all();
    }

    /**
     * Retrieves MenuLinkType instances by an array of menu link type names.
     *
     * @param array $linkTypeNames
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveByNames(array $linkTypeNames)
    {
        return MenuLinkType::whereIn('name', $linkTypeNames)->get();
    }

    /**
     * Retrieves a MenuLinkType instance by a name.
     *
     * @param string $linkTypeName
     * @return \Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType
     */
    public function retrieveByName($linkTypeName)
    {
        return MenuLinkType::whereName($linkTypeName)->get()->first();
    }

    /**
     * Retrieves a MenuLinkType instance by ID.
     *
     * @param int $linkTypeId
     * @return \Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType
     */
    public static function retrieveStatic($linkTypeId)
    {
        return MenuLinkType::find($linkTypeId);
    }
}