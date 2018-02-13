<?php

namespace Photon\PhotonCms\Core\Entities\NativeClassAttribute;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class NativeClassAttribute
{
    /**
     * Attribute name
     *
     * @var string
     */
    protected $name;

    /**
     * Attribute default value
     *
     * @var mixed
     */
    protected $default;

    /**
     * Flag that indicates if the default value is used for the attribute.
     * This is to avoid a false negative when checking for default value and the value is NULL.
     *
     * @var boolean
     */
    protected $hasDefault = false;

    /**
     * Visibility of the attribute within a class.
     * Possible values: public, private, protected
     *
     * @var string
     */
    protected $visibility;

    /**
     * All allowed visibility values.
     *
     * @var array
     */
    protected $allowedVisibilityValues = [
        'public',
        'private',
        'protected'
    ];

    /**
     * [Something smart]
     *
     * @param string $name
     * @param mixed $default
     * @param string $visibility
     */
    public function __construct(
        $name,
        $default = null,
        $visibility = 'public'
    )
    {
        $this->name       = $name;
        $this->default    = $default;
        $this->visibility = $visibility;

        if (!in_array($visibility, $this->allowedVisibilityValues)) {
            throw new PhotonException('INVALID_VISIBILITY_VALUE');
        }
    }

    /**
     * Returns attribute name.
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Sets the attribute default value.
     *
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
        $this->hasDefault = true;
    }

    /**
     * Returns the attribute default value.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Indicates if the attribute uses a default value.
     * This is to avoid a false negative when checking against the default value and the value is NULL.
     *
     * @return boolean
     */
    public function hasDefault()
    {
        return $this->hasDefault;
    }

    /**
     * Sets the attribute visibility.
     * Available values: public, private, protected
     *
     * @param string $visibility
     */
    function setVisibility($visibility)
    {
        if (!in_array($visibility, $this->allowedVisibilityValues)) {
            throw new PhotonException('INVALID_VISIBILITY_VALUE');
        }
        $this->visibility = $visibility;
    }

    /**
     * Sets the attribute visibility to private.
     */
    public function setPrivate()
    {
        $this->visibility = 'private';
    }

    /**
     * Sets the visibility to public.
     */
    public function setPublic()
    {
        $this->visibility = 'public';
    }

    /**
     * Sets the visibility to protected.
     */
    public function setProtected()
    {
        $this->visibility = 'protected';
    }

    /**
     * Checks if visibility is set to private.
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return $this->visibility === 'private';
    }

    /**
     * Checks if visibility is public.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return $this->visibility === 'public';
    }

    /**
     * Checks if visibility is protected.
     *
     * @return boolean
     */
    public function isProtected()
    {
        return $this->visibility === 'protected';
    }
}