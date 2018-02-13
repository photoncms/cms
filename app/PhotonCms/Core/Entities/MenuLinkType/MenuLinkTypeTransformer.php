<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType;

use Photon\PhotonCms\Core\Transform\BaseTransformer;
//use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformInterface;
//use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformConvertedInterface;

use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeDataController;

/**
 * Transforms MenuLinkType instances into various output packages.
 */
class MenuLinkTypeTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var \Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkType $object
     * @return array
     */
    public function transform(MenuLinkType $object)
    {
        $objectArray = [
            'id' => (int) $object->id,
            'name' => $object->name,
            'title' => $object->title,
            'clickable' => (bool) $object->clickable,
            'is_system' => (bool) $object->is_system
        ];

        $objectArray['has_generic_icon'] = MenuLinkTypeDataController::hasGenericIconByTypeId($object->id);

        return $objectArray;
    }
}