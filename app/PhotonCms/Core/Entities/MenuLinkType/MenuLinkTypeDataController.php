<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\LinkTypeHandlerGetDataInterface;
use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\LinkTypeHandlerCompileLinkInterface;
use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeRepository;
use Photon\PhotonCms\Core\Entities\MenuItem\MenuItem;

class MenuLinkTypeDataController
{

    /**
     * Gets menu link type resource data by menu link type.
     *
     * @param string $typeName
     * @return mixed
     */
    public static function getDataByTypeName($typeName)
    {
        $typeClassName = self::getTypeHandlerClassNameByTypeName($typeName);

        $menuLinkDataTypeHandler = new $typeClassName();

        $menuLinkResources = [];
        if ($menuLinkDataTypeHandler instanceof LinkTypeHandlerGetDataInterface) {
            $menuLinkResources = $menuLinkDataTypeHandler->getData();
        }

        return [
            'type' => $menuLinkDataTypeHandler->getType(),
            'resources' => $menuLinkResources
        ];
    }

    /**
     * Compiles a link from provided data by menu link type id.
     *
     * @param string $data
     * @param int $typeId
     * @return string
     */
    public static function compileLinkFromDataByTypeId($data, $typeId)
    {
        $menuLinkType = self::getTypeByid($typeId);

        $typeClassName = self::getTypeHandlerClassNameByTypeName($menuLinkType->name);

        $menuLinkDataTypeHandler = new $typeClassName();

        if ($menuLinkDataTypeHandler instanceof LinkTypeHandlerCompileLinkInterface) {
            return $menuLinkDataTypeHandler->compileLinkFromData($data);
        }

        return null;
    }

    /**
     * Extracts an icon from a Menu Item using its menu link type handler.
     *
     * @param MenuItem $item
     * @param int $typeId
     * @return sring
     */
    public static function extractIconFromMenuItemByTypeId(MenuItem $item, $typeId)
    {
        $menuLinkType = self::getTypeByid($typeId);

        $typeClassName = self::getTypeHandlerClassNameByTypeName($menuLinkType->name);

        $menuLinkDataTypeHandler = new $typeClassName();

        return $menuLinkDataTypeHandler->extractIcon($item);
    }

    /**
     * Checks if the menu link type provides a generic icon for its data type.
     *
     * @param int $typeId
     * @return boolean
     */
    public static function hasGenericIconByTypeId($typeId)
    {
        $menuLinkType = self::getTypeByid($typeId);

        $typeClassName = self::getTypeHandlerClassNameByTypeName($menuLinkType->name);

        $menuLinkDataTypeHandler = new $typeClassName();

        return $menuLinkDataTypeHandler->hasGenericIcon();
    }

    /**
     * Gets a menu link type by ID.
     *
     * @param int $typeId
     * @return \Photon\PhotonCme\Core\Entities\MenuLinkType\MenuLinkType
     * @throws PhotonException
     */
    private static function getTypeByid($typeId)
    {
        $menuLinkType = MenuLinkTypeRepository::findStatic($typeId, 'Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeGateway');

        if (!$menuLinkType) {
            throw new PhotonException('MENU_LINK_TYPE_NOT_FOUND', ['id' => $typeId]);
        }

        return $menuLinkType;
    }

    /**
     * Gets a menu link type handler class name by menu link type name.
     *
     * @param string $typeName
     * @return string
     * @throws PhotonException
     */
    private static function getTypeHandlerClassNameByTypeName($typeName)
    {
        $typeClassName = str_replace(' ', '', ucwords(str_replace('_', ' ', $typeName)));
        $typeClassName = '\Photon\PhotonCms\Core\Entities\MenuLinkType\LinkTypeHandlers\\'.$typeClassName.'Handler';

        if (!class_exists($typeClassName)) {
            throw new PhotonException('MENU_LINK_TYPE_HANDLER_NOT_FOUND', ['handler_class_name' => $typeClassName]);
        }

        return $typeClassName;
    }
}