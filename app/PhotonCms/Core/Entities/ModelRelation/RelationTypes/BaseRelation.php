<?php

namespace Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes;

class BaseRelation
{
    /**
     * Name of the related field.
     *
     * @var sring
     */
    protected $relationName;

    protected $requiresPivot = false;

    protected $hasAttribute = false;

    /**
     * Name of the target field from the target table.
     *
     * @var string
     */
    protected $targetField = 'id';

    public function getRelationName()
    {
        return $this->relationName;
    }

    public function requiresPivot()
    {
        return $this->requiresPivot;
    }

    public function hasAttribute()
    {
        return $this->hasAttribute;
    }

    public function getFieldType()
    {
        return $this->fieldType;
    }

    public function getTargetTable()
    {
        return $this->targetTable;
    }
}