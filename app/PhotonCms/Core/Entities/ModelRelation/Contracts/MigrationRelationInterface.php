<?php

namespace Photon\PhotonCms\Core\Entities\ModelRelation\Contracts;

interface MigrationRelationInterface
{

    /**
     * Compiles the relation into a string representing the model code for a relation.
     *
     * @return string
     */
    public function compileMigration();
}