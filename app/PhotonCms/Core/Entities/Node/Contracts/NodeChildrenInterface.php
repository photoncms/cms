<?php

namespace Photon\PhotonCms\Core\Entities\Node\Contracts;

use Photon\PhotonCms\Core\Entities\Node\Node;

/**
 *
 * @author Sasa
 */
interface NodeChildrenInterface
{

    /**
     * Retrieves all child nodes.
     *
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $node
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveChildren(Node $node);
}