<?php

namespace Photon\PhotonCms\Core\InstanceComparator\Contracts;

interface InstanceComparatorInterface
{
    /**
     * Compare method for two instances of an entity.
     *
     * Returns an array of differences along with the change type (add, delete, update).
     * Each Class should perform its own compare() input parameters to avoid unhandled exceptions.
     *
     * @return array
     */
    public function compare();
}