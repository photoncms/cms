<?php

namespace Photon\PhotonCms\Core\Entities\Node;

use Photon\PhotonCms\Core\Transform\BaseTransformer;

/**
 * Transforms Node instances into various output packages.
 */
class NodeTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var \Photon\PhotonCms\Core\Entities\Node\Node $object
     * @return array
     */
    public function transform(Node $object)
    {
        $objectArray = $object->toArray();

        $this->transformGenericObjects($objectArray);

        return $objectArray;
    }

    /**
     * Transforms the node to a JSTree-workable node array.
     *
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $object
     * @return array
     */
    public function transformForJSTreeNode(Node $object)
    {
        $objectArray = [
            'id' => $object->id,
            'anchor_text' => $object->anchor_text,
            'anchor_html' => $object->anchor_html,
            'table_name' => $object->getTable(),
            'scope_id' => $object->scope_id
        ];

        return $objectArray;
    }

    /**
     * Transforms the node into a JSTree-workable ancestor node array.
     *
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $object
     * @return array
     */
    public function transformForJSTreeAncestor(Node $object)
    {
        $objectArray = [
            'id' => $object->id,
            'table_name' => $object->getTable(),
            'scope_id' => $object->scope_id
        ];

        return $objectArray;
    }
}
