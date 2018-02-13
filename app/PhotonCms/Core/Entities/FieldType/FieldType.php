<?php

namespace Photon\PhotonCms\Core\Entities\FieldType;

use Illuminate\Database\Eloquent\Model;

class FieldType extends Model
{
    /**
     * Determines if the field type represents an attribute of a given model.
     * Set this value to false when working with relations which don't have a
     * owning model attribute, like many to many or one to many.
     *
     * @var boolean
     */
    protected $isAttribute = true;

    /**
     * Determines if the field type is a relation.
     *
     * @var boolean
     */
    protected $isRelation = false;

    /**
     * Type of a relation.
     * Available options:
     * - OneToMany
     * - ManyToOne
     * - ManyToMany
     *
     * @var string
     */
    protected $relationType;

    /**
     * Determines if the field type, which is a relation, requires a pivot table.
     * No point in using this attribute if the field type is not a relation.
     *
     * @var boolean
     */
    protected $requiresPivot = false;

    /**
     * Checks if the field type is a relation.
     *
     * @return boolean
     */
    public function isRelation()
    {
        return $this->isRelation;
    }

    /**
     * Checks if the field type requires a pivot table.
     * No point in calling this method if the field type is not a relation.
     *
     * @return boolean
     */
    public function requiresPivot()
    {
        return $this->requiresPivot;
    }

    /**
     * Checks if the field type has a column in the containing module table.
     *
     * @return boolean
     */
    public function isAttribute()
    {
        return $this->isAttribute;
    }

    /**
     * Returns the type of a relation.
     * Available options:
     * - OneToMany
     * - ManyToOne
     * - ManyToMany
     *
     * @return string
     */
    public function getRelationType()
    {
        return $this->relationType;
    }
}