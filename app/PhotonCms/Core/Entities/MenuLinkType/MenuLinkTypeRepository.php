<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\MenuLinkTypeGatewayInterface;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over MenuLinkType entity.
 */
class MenuLinkTypeRepository
{

    /**
     * Get all available MenuLinkType instances.
     *
     * @param MenuLinkTypeGatewayInterface $gateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(MenuLinkTypeGatewayInterface $gateway)
    {
        return $gateway->retrieveAll();
    }

    /**
     * Find IDs of MenuLinkType instances by an array of names.
     *
     * @param array $linkTypeNames
     * @param MenuLinkTypeGatewayInterface $gateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findIdsByNames(array $linkTypeNames, MenuLinkTypeGatewayInterface $gateway)
    {
        $menuLinkTypes = $this->findByNames($linkTypeNames, $gateway);

        $ids = [];
        foreach ($menuLinkTypes as $menuLinkType) {
            $ids[] = $menuLinkType->id;
        }

        return $ids;
    }

    /**
     * Finds all MenuLinkType instances by an array of names.
     *
     * @param array $linkTypeNames
     * @param MenuLinkTypeGatewayInterface $gateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByNames(array $linkTypeNames, MenuLinkTypeGatewayInterface $gateway)
    {
        return $gateway->retrieveByNames($linkTypeNames);
    }

    /**
     * Find a MenuLinkType instance by a name.
     *
     * @param type $linkTypeName
     * @param MenuLinkTypeGatewayInterface $gateway
     * @return \Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType
     */
    public function findByName($linkTypeName, MenuLinkTypeGatewayInterface $gateway)
    {
        return $gateway->retrieveByName($linkTypeName);
    }

    /**
     * Finds a MenuLinkType instance by ID.
     *
     * @param int $linkTypeId
     * @param string $gatewayClassName
     * @return \Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType
     * @throws PhotonException
     */
    public static function findStatic($linkTypeId, $gatewayClassName)
    {
        if (is_string($gatewayClassName) && !class_exists($gatewayClassName)) {
            throw new PhotonException('CLASS_DOESNT_EXIST', ['class' => $gatewayClassName]);
        }
        
        return $gatewayClassName::retrieveStatic($linkTypeId);
    }
}