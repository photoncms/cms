<?php

namespace Photon\PhotonCms\Core\Entities\Node\Contracts;

interface RootNodesInterface
{

    /**
     * Retrieves a node instance from the DB.
     *
     * @param int $id
     * @return mixed
     */
    public function retrieve($id);

    /**
     * Retrieves all root nodes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveRootNodes($filterData = null);
}