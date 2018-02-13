<?php

namespace Photon\PhotonCms\Core\Entities\Model\Contracts;

use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassTemplateInterface;

interface ModelTemplateInterface extends NativeClassTemplateInterface
{

    /**
     * Model name getter.
     *
     * @return string
     */
    public function getModelName();

    /**
     * Model name setter.
     *
     * @param string $modelName
     */
    public function setModelName($modelName);

    /**
     * Sets model name from table name.
     */
    public function setModelNameFromTableName();

    /**
     * Table name getter.
     *
     * @return string
     */
    public function getTableName();

    /**
     * Table name setter.
     *
     * @param string $tableName
     */
    public function setTableName($tableName);

    /**
     * Returns an array of ModelRelationInterface instances.
     *
     * @return array
     */
    public function getRelations();

    /**
     * Sets an array of ModelRelationInterface instances.
     *
     * @param array $relations
     */
    public function addRelations(array $relations);

    /**
     * Adds an instance of ModelRelationInterface to the array of all relations for this model.
     *
     * @param \Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationInterface $relation
     */
    public function addRelation(ModelRelationInterface $relation);

    /**
     * Indicates if the model has any relations assigned.
     *
     * @return type
     */
    public function hasRelations();

    /**
     * Says if the model uses soft deletes.
     *
     * @return boolean
     */
    public function usesSoftDeletes();

    /**
     * Sets the model to use soft deletes.
     */
    public function setSoftDeletes();
}