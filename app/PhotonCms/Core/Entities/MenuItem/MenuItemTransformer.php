<?php

namespace Photon\PhotonCms\Core\Entities\MenuItem;

use Photon\PhotonCms\Core\Transform\BaseTransformer;
//use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformInterface;
//use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformConvertedInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Entities\Node\Node;

/**
 * Transforms Menu instances into various output packages.
 */
class MenuItemTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem $object
     * @return array
     */
    public function transform(MenuItem $object)
    {
        $object->load(["created_by", "updated_by"]);

        $objectArray = [
            'id' => ($object->id !== null) ? (int) $object->id : $object->id,
            'lft' => (int) $object->lft,
            'rgt' => (int) $object->rgt,
            'parent_id' => ($object->parent_id !== null) ? (int) $object->parent_id : $object->parent_id,
            'depth' => (int) $object->depth,
            'menu_name' => (is_object($object->menu)) ? $object->menu->name : null,
            'menu_link_type_name' => (is_object($object->menu_link_type)) ? $object->menu_link_type->name : null,
            'title' => $object->title,
            'resource_data' => $object->resource_data,
            'entry_data' => $object->entry_data,
            'icon' => $object->getIcon(),
            'slug' => $object->slug,
            'link' => $object->getCompiledLink(),
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

        if ($object instanceof Node) {
            $objectArray['has_children'] = !$object->children->isEmpty();
            $objectArray['clickable'] = (bool) $object->isClickable();
        }

        // Add any relations preloaded with eager loading
        $relations = $object->getRelations();
        if (isset($relations['children'])) {
            unset($relations['children']);
        }
        $result = array_merge($objectArray, $relations);

        $this->transformGenericObjects($result);

        return $result;
    }

    /**
     * Transforms the node into a JSTree-workable ancestor node array.
     *
     * @param \Photon\PhotonCms\Core\Entities\MenuItem\MenuItem $object
     * @return array
     */
    public function transformForJSTreeAncestor(MenuItem $object)
    {
        $objectArray = [
            'id' => $object->id
        ];

        return $objectArray;
    }
}