<?php

namespace Photon\PhotonCms\Core\Entities\Model;

use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassTemplate;
use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationInterface;

class ModelTemplate extends NativeClassTemplate implements ModelTemplateInterface
{
    
    /**
     * Name of the model DB table.
     *
     * @var string
     */
    protected $tableName = '';
    
    /**
     * Array of required relation instances of ModelRelationInterface.
     * Contains relations in following format [relationName]=>[instance of ModelRelationInterface]
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Flag which determines if the model will support soft deleting.
     *
     * @var boolean
     */
    protected $useSoftDeletes = false;

    public function __construct()
    {
    }

    /**
     * Model name getter.
     * 
     * @return string
     */
    public function getModelName()
    {
        return $this->className;
    }

    /**
     * Model name setter.
     * 
     * @param string $modelName
     */
    public function setModelName($modelName)
    {
        $this->className = $modelName;
    }

    /**
     * Sets model name from table name.
     */
    public function setModelNameFromTableName()
    {
        $this->className = str_singular(studly_case($this->tableName));
    }

    /**
     * Table name getter.
     * 
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Table name setter.
     * 
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Returns an array of ModelRelationInterface instances.
     * 
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    public function addRelations(array $relations)
    {
        foreach ($relations as $relation) {
            $this->addRelation($relation);
        }
    }
    
    /**
     * Adds an instance of ModelRelationInterface to the array of all relations for this model.
     * 
     * @param \Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationInterface $relation
     */
    public function addRelation(ModelRelationInterface $relation)
    {
        $this->relations[$relation->getRelationName()] = $relation;
    }

    /**
     * Indicates if the model has any relations assigned.
     *
     * @return type
     */
    public function hasRelations()
    {
        return is_array($this->relations) && !empty($this->relations);
    }

    /**
     * Says if the model uses soft deletes.
     *
     * @return boolean
     */
    public function usesSoftDeletes()
    {
        return $this->useSoftDeletes;
    }

    /**
     * Sets the model to use soft deletes.
     */
    public function setSoftDeletes()
    {
        $this->assignTrait('\Illuminate\Database\Eloquent\SoftDeletes');
        $this->useSoftDeletes = true;
    }
}