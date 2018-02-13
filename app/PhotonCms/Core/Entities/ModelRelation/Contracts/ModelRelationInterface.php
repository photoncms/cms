<?php

namespace Photon\PhotonCms\Core\Entities\ModelRelation\Contracts;

interface ModelRelationInterface
{

    /**
     * Compiles the relation into a string representing the model code for a relation.
     *
     * @return string
     */
    public function compile();

    public function getRelationName();

    public function requiresPivot();
}