<?php

namespace Photon\PhotonCms\Core\Entities\Node;

use Baum\Node as BaumNode;

class Node extends BaumNode
{

    /**
     * Guard NestedSet fields from mass-assignment.
     *
     * @var array
     */
    protected $guarded = ['id', 'parent_id', 'lft', 'rgt', 'depth', 'scope_id'];

    /**
     * Compares two nodes classes to see if they fit
     *
     * @param $node
     * @return bool
     */
    public function isTheSame($node)
    {
        return get_class($this) === get_class($node);
    }
}