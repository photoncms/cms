<?php

namespace Photon\PhotonCms\Core\Entities\ModelAttribute;

use Photon\PhotonCms\Core\Entities\NativeClassAttribute\NativeClassAttribute;
use Photon\PhotonCms\Core\Entities\FieldType\FieldType;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;

class ModelAttribute extends NativeClassAttribute
{
    /**
     * Value type used in laravel migrations.
     *
     * @var string
     */
    private $laravelType;


    /**
     * An instance of Photon field type for this attribute
     *
     * @var \Photon\PhotonCms\Core\Entities\FieldType\FieldType
     */
    private $fieldType;

    /**
     * Indicates if the value can be NULL.
     *
     * @var boolean
     */
    private $nullable;

    /**
     * Indicates if the attribute should be indexed in the DB.
     *
     * @var boolean
     */
    private $indexed;

    /**
     * Indicates if the value is fillable through the model constructor.
     *
     * @var boolean
     */
    private $fillable;

    /**
     * Indicates if the value must be unique in its column.
     *
     * @var boolean
     */
    private $unique;

    private $parameters;

    /**
     * $fieldType can be passed here as an instance of \Photon\PhotonCms\Core\Entities\FieldType\FieldType
     * or as an ID of an instance and it will be loaded automatically when the getter is calles.
     *
     * @param string $name
     * @param int|FieldType $fieldType
     * @param string $laravelType
     * @param string $visibility
     * @param mixed $default
     * @param bool $nullable
     * @param bool $indexed
     * @param bool $fillable
     * @param bool $unique
     * @param mixed $parameters
     */
    public function __construct(
        $name,
        $fieldType,
        $laravelType = null,
        $visibility = 'public',
        $default = null,
        $nullable = true,
        $indexed = false,
        $fillable = true,
        $unique = false,
        $parameters = []
    )
    {
        $this->name        = $name;
        $this->fieldType   = $fieldType;
        $this->laravelType = $laravelType;
        $this->visibility  = $visibility;
        if($default || $nullable) {
            $this->default     = $default;
            $this->hasDefault  = 1;
        }
        $this->nullable    = $nullable;
        $this->indexed     = $indexed;
        $this->fillable    = $fillable;
        $this->unique      = $unique;
        $this->parameters  = $parameters;
    }

    /**
     * Retrieves the laravel type of the attribute used in laravel migrations.
     *
     * @return string
     */
    public function getLaravelType()
    {
        if (!$this->laravelType) {
            return $this->getFieldType()->laravel_type;
        }
        return $this->laravelType;
    }

    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function getFieldType()
    {
        if ($this->fieldType instanceof FieldType) {
            return $this->fieldType;
        }
        elseif (is_numeric($this->fieldType) && $this->fieldType > 0) {
            $this->fieldType = FieldTypeRepository::findByIdStatic($this->fieldType);

            return $this->fieldType;
        }
    }

    /**
     * Indicates if the attribute value can be NULL.
     *
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * Indicates if the attribute should be indexed in the DB.
     *
     * @return boolean
     */
    public function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * Indicates if attribute is fillable through model constructor.
     *
     * @return boolean
     */
    public function isFillable()
    {
        return $this->fillable;
    }

    /**
     * Indicates if the attribute value must be unique within its column.
     *
     * @return boolean
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * On model generation, attributes which return true to this method will be set to return the value as Carbon.
     *
     * @return type
     */
    public function isDate()
    {
        $consideredAsDate = [
            'date',
            'dateTime',
            'dateTimeTz',
            'timestamp',
            'timestampTz',
        ];
        return in_array($this->laravelType, $consideredAsDate);
    }

    public function hasParameters()
    {
        return is_array($this->parameters) && !empty($this->parameters);
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
