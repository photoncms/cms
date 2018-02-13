<?php

namespace Photon\PhotonCms\Core\Entities\Field\Migrations;

use \Photon\PhotonCms\Core\Entities\Migration\BaseMigrationTemplate;

class FieldUpdateMigrationTemplate extends BaseMigrationTemplate
{
    private $fields = [];
    private $tableName;

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getFields()
    {
        return array_keys($this->fields);
    }

    public function toNullable($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            return $this->fields[$fieldName][$fieldName]['change_to_nullable'];
        }
    }

    public function toNotNullable($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            return $this->fields[$fieldName][$fieldName]['change_to_not_nullable'];
        }
    }

    public function defaultValueChanged($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            return $this->fields[$fieldName][$fieldName]['change_default_value'];
        }
    }

    public function getDefaultValue($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            return $this->fields[$fieldName][$fieldName]['default_value'];
        }
    }

    public function dropDefault($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            return $this->fields[$fieldName][$fieldName]['drop_default'];
        }
    }

    public function setField($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            $this->fields[$fieldName] = [
                'change_to_nullable' => false,
                'change_to_not_nullable' => false,
                'change_default_value' => false,
                'default_value' => null,
                'drop_default' => false
            ];
        }
    }

    public function changeToNullable($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            $this->fields[$fieldName]['change_to_nullable'] = true;
            $this->fields[$fieldName]['change_to_not_nullable'] = false;
        }
    }

    public function changeToNotNullable($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            $this->fields[$fieldName]['change_to_not_nullable'] = true;
            $this->fields[$fieldName]['change_to_nullable'] = false;
        }
    }

    public function setDefault($fieldName, $defaultValue)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            $this->fields[$fieldName]['change_default_value'] = true;
            $this->fields[$fieldName]['default_value'] = $defaultValue;
            $this->fields[$fieldName]['drop_default'] = false;
        }
    }

    public function unsetDefault($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            $this->fields[$fieldName]['drop_default'] = true;
            $this->fields[$fieldName]['default_value'] = null;
        }
    }
}