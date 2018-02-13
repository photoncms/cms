<?php

namespace Photon\PhotonCms\Core\Entities\Field\Migrations;

class FieldUpdateTemplate
{
    private $fieldName;
    private $laravelType;
    private $changeToNullable = false;
    private $changeToNotNullable = false;
    private $changeDefaultValue = false;
    private $defaultValue = null;
    private $dropDefaultValue = false;

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function getLaravelType()
    {
        return $this->laravelType;
    }

    public function setLaravelType($laravelType)
    {
        $this->laravelType = $laravelType;
    }

    public function toNullable()
    {
        return $this->changeToNullable;
    }

    public function toNotNullable()
    {
        return $this->changeToNotNullable;
    }

    public function defaultValueChanged()
    {
        return $this->changeDefaultValue;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function dropDefault()
    {
        return $this->dropDefaultValue;
    }

    public function changeToNullable()
    {
        $this->changeToNullable = true;
        $this->changeToNotNullable = false;
    }

    public function changeToNotNullable()
    {
        $this->changeToNullable = false;
        $this->changeToNotNullable = true;
    }

    public function setDefault($defaultValue)
    {
        $this->changeDefaultValue = true;
        $this->defaultValue = $defaultValue;
        $this->dropDefaultValue = false;
    }

    public function unsetDefault()
    {
        $this->dropDefaultValue = true;
        $this->defaultValue = null;
    }
}