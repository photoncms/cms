<?php

namespace Photon\PhotonCms\Core\Entities\Menu;

use Photon\PhotonCms\Core\Transform\BaseTransformer;
//use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformInterface;
//use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformConvertedInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Transforms Menu instances into various output packages.
 */
class MenuTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var \Photon\PhotonCms\Core\Entities\Menu\Menu $object
     * @return array
     */
    public function transform(Menu $object)
    {
        $object->load(["created_by", "updated_by"]);

        $objectArray = [
            'id' => (int) $object->id,
            'name' => $object->name,
            'title' => $object->title,
            'max_depth' => $object->max_depth,
            'min_root' => $object->min_root,
            'is_system' => (bool) $object->is_system,
            'description' => $object->description,
            'created_at' => $object->created_at,
            'updated_at' => $object->updated_at,
            "permission_control" => [
                "edit_restrictions" => [],
                "crud" => [
                    "create" => true,
                    "update" => true,
                    "delete" => true
                ]
            ]
        ];

        // Add any relations preloaded with eager loading
        $result = array_merge($objectArray, $object->getRelations());

        $this->transformGenericObjects($result);

        return $result;
    }
}