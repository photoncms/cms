<?php

namespace Photon\PhotonCms\Core\Entities\Migration;

use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassTemplate;
use Photon\PhotonCms\Core\Entities\ModelAttribute\ModelAttribute;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationTemplateInterface;
use Photon\PhotonCms\Core\Helpers\StringConversionsHelper;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\MigrationRelationInterface;

class MigrationTemplate extends NativeClassTemplate implements MigrationTemplateInterface
{
    /**
     * An array of tables for creation where indexes represent the name of the table and values represent arrays of attribute objects.
     *
     * @var array
     */
    private $tablesForCreation = [];

    private $tablesForUpdate = [];

    private $tablesForRemoval = [];

    public function __construct()
    {
        $this->setInheritance('Illuminate\Database\Migrations\Migration');
        $this->assignUse('Illuminate\Database\Schema\Blueprint');
    }

    /**
     * Returns a specified fileName for a model file.
     * Empty string will be returned for no fileName.
     *
     * @return string
     */
    public function getFilename()
    {
        if (!$this->usesFileName) {
            return date('Y_m_d_His').'_'.StringConversionsHelper::camelCaseToSnakeCase($this->className).'.php';
        }
        return $this->fileName;
    }


    /**
     * Retrieves an array of tables for creation where indexes represent the name of the table and values represent arrays of attribute objects.
     *
     * @return array
     */
    public function getTablesForCreation()
    {
        return array_keys($this->tablesForCreation);
    }

    /**
     * This is a very complex approach, avoid using this setter!
     *
     * @param array $tablesForCreation
     */
    public function setTablesForCreation(array $tablesForCreation)
    {
        $this->tablesForCreation = $tablesForCreation;
    }

    /**
     * Adds a table for creation with its attribute objects.
     *
     * @param string $tableName
     * @param array $tableFields
     */
    public function addTableForCreation($tableName, array $tableFields = [], array $tableRelations = [])
    {
        $this->addTableForCreationWithFields($tableName, $tableFields);
        $this->addTableForCreationWithRelations($tableName, $tableRelations);
    }

    public function addTableForCreationWithFields($tableName, array $tableFields = [])
    {
        foreach ($tableFields as $tableField) {
            $this->addTableForCreationField($tableName, $tableField);
        }
    }

    public function addTableForCreationWithRelations($tableName, array $tableRelations = [])
    {
        foreach ($tableRelations as $tableRelation) {
            $this->addTableForCreationRelation($tableName, $tableRelation);
        }
    }

    /**
     * Adds an attribute object to a table which will be created in the migration.
     *
     * @param string $tableName
     * @param MigrationField $tableField
     * @throws PhotonException
     */
    public function addTableForCreationField($tableName, ModelAttribute $tableField)
    {
//        if (!key_exists($tableName, $this->tablesForCreation)) {
//            $this->tablesForCreation[$tableName] = [];
//        }
        $this->tablesForCreation[$tableName]['fields'][] = $tableField;
    }

    /**
     * Retrieves an array of attribute objects for a specific table.
     *
     * @param string $tableName
     * @return array
     * @throws PhotonException
     */
    public function getTableForCreationFields($tableName)
    {
        if (!key_exists($tableName, $this->tablesForCreation)) {
            throw new PhotonException('TABLE_NOT_SET_FOR_CREATION');
        }
        if (!key_exists('fields', $this->tablesForCreation[$tableName])) {
            return [];
        }

        return $this->tablesForCreation[$tableName]['fields'];
    }

    public function addTableForCreationRelation($tableName, MigrationRelationInterface $modelRelation)
    {
//        if (!key_exists($tableName, $this->tablesForCreation)) {
//            $this->tablesForCreation[$tableName] = [];
//        }
        $this->tablesForCreation[$tableName]['relations'][] = $modelRelation;
    }

    public function getTableForCreationRelations($tableName)
    {
        if (!key_exists($tableName, $this->tablesForCreation)) {
            throw new PhotonException('TABLE_NOT_SET_FOR_CREATION');
        }
        if (!key_exists('relations', $this->tablesForCreation[$tableName])) {
            return [];
        }

        return $this->tablesForCreation[$tableName]['relations'];
    }





    public function getTablesForUpdate()
    {
        return array_keys($this->tablesForUpdate);
    }

    public function setTablesForUpdate(array $tablesForUpdate)
    {
        $this->tablesForUpdate = $tablesForUpdate;
    }

    public function addTableForUpdate($tableName, $fieldsForCreation = [], $fieldsForUpdate = [], $fieldsForRemoval = [], $relationsForCreation = [], $relationsForRemoval = [])
    {
        $this->addTableForUpdateFieldsForCreation($tableName, $fieldsForCreation);
        $this->addTableForUpdateFieldsForUpdate($tableName, $fieldsForUpdate);
        $this->addTableForUpdateFieldsForRemoval($tableName, $fieldsForRemoval);
        $this->addTableForUpdateRelationsForCreation($tableName, $relationsForCreation);
        $this->addTableForUpdateRelationsForRemoval($tableName, $relationsForRemoval);
    }

    public function addTableForUpdateFieldsForCreation($tableName, $tableFields)
    {
        foreach ($tableFields as $tableField) {
            $this->addTableForUpdateFieldForCreation($tableName, $tableField);
        }
    }

    public function addTableForUpdateFieldForCreation($tableName, ModelAttribute $tableField)
    {
        $this->tablesForUpdate[$tableName]['fields']['creation'][] = $tableField;
    }

    public function getTableForUpdateFieldsForCreation($tableName)
    {
        if (!key_exists($tableName, $this->tablesForUpdate)) {
            throw new PhotonException('TABLE_NOT_SET_FOR_UPDATE');
        }
        if (!key_exists('fields', $this->tablesForUpdate[$tableName]) || !key_exists('creation', $this->tablesForUpdate[$tableName]['fields']) ) {
            return [];
        }

        return $this->tablesForUpdate[$tableName]['fields']['creation'];
    }

    public function addTableForUpdateFieldsForUpdate($tableName, $tableFields)
    {
        foreach ($tableFields as $tableField) {
            $this->addTableForUpdateFieldForUpdate($tableName, $tableField);
        }
    }

    public function addTableForUpdateFieldForUpdate($tableName, $tableField)
    {
        $this->tablesForUpdate[$tableName]['fields']['update'][] = $tableField;
    }

    public function getTableForUpdateFieldsForUpdate($tableName)
    {
        if (!key_exists($tableName, $this->tablesForUpdate)) {
            throw new PhotonException('TABLE_NOT_SET_FOR_UPDATE');
        }
        if (!key_exists('fields', $this->tablesForUpdate[$tableName]) || !key_exists('update', $this->tablesForUpdate[$tableName]['fields']) ) {
            return [];
        }

        return $this->tablesForUpdate[$tableName]['fields']['update'];
    }

    public function addTableForUpdateFieldsForRemoval($tableName, $tableFields)
    {
        foreach ($tableFields as $tableField) {
            $this->addTableForUpdateFieldForRemoval($tableName, $tableField);
        }
    }

    public function addTableForUpdateFieldForRemoval($tableName, $tableField)
    {
        $this->tablesForUpdate[$tableName]['fields']['removal'][] = $tableField;
    }

    public function getTableForUpdateFieldsForRemoval($tableName)
    {
        if (!key_exists($tableName, $this->tablesForUpdate)) {
            throw new PhotonException('TABLE_NOT_SET_FOR_UPDATE');
        }
        if (!key_exists('fields', $this->tablesForUpdate[$tableName]) || !key_exists('removal', $this->tablesForUpdate[$tableName]['fields']) ) {
            return [];
        }

        return $this->tablesForUpdate[$tableName]['fields']['removal'];
    }

    public function addTableForUpdateRelationsForCreation($tableName, $tableRelations)
    {
        foreach ($tableRelations as $tableRelation) {
            $this->addTableForUpdateRelationForCreation($tableName, $tableRelation);
        }
    }

    public function addTableForUpdateRelationForCreation($tableName, MigrationRelationInterface $tableRelation)
    {
        $this->tablesForUpdate[$tableName]['relations']['creation'][] = $tableRelation;
    }

    public function getTableForUpdateRelationsForCreation($tableName)
    {
        if (!key_exists($tableName, $this->tablesForUpdate)) {
            throw new PhotonException('TABLE_NOT_SET_FOR_UPDATE');
        }
        if (!key_exists('relations', $this->tablesForUpdate[$tableName]) || !key_exists('creation', $this->tablesForUpdate[$tableName]['relations']) ) {
            return [];
        }

        return $this->tablesForUpdate[$tableName]['relations']['creation'];
    }

    public function addTableForUpdateRelationsForRemoval($tableName, $tableRelations)
    {
        foreach ($tableRelations as $tableRelation) {
            $this->addTableForUpdateRelationForRemoval($tableName, $tableRelation);
        }
    }

    public function addTableForUpdateRelationForRemoval($tableName, $tableRelation)
    {
        $this->tablesForUpdate[$tableName]['relations']['removal'][] = $tableRelation;
    }

    public function getTableForUpdateRelationsForRemoval($tableName)
    {
        if (!key_exists($tableName, $this->tablesForUpdate)) {
            throw new PhotonException('TABLE_NOT_SET_FOR_UPDATE');
        }
        if (!key_exists('relations', $this->tablesForUpdate[$tableName]) || !key_exists('removal', $this->tablesForUpdate[$tableName]['relations']) ) {
            return [];
        }

        return $this->tablesForUpdate[$tableName]['relations']['removal'];
    }


    public function getTablesForRemoval()
    {
        return array_keys($this->tablesForRemoval);
    }

    public function setTablesForRemoval(array $tablesForRemoval)
    {
        $this->tablesForRemoval = $tablesForRemoval;
    }

    public function addTableForRemoval($tableName, $relationsForRemoval = [])
    {
        if (!in_array($tableName, $this->tablesForRemoval)) {
            $this->tablesForRemoval[$tableName] = [];
        }
        $this->addTableForRemovalRelationsForRemoval($tableName, $relationsForRemoval);
    }

    public function addTableForRemovalRelationsForRemoval($tableName, $tableRelations)
    {
        foreach ($tableRelations as $tableRelation) {
            $this->addTableForRemovalRelationForRemoval($tableName, $tableRelation);
        }
    }

    public function addTableForRemovalRelationForRemoval($tableName, $tableRelation)
    {
        $this->tablesForRemoval[$tableName]['relations']['removal'][] = $tableRelation;
    }

    public function getTableForRemovalRelationsForRemoval($tableName)
    {
        if (!key_exists($tableName, $this->tablesForRemoval)) {
            throw new PhotonException('TABLE_NOT_SET_FOR_REMOVAL');
        }
        if (!key_exists('relations', $this->tablesForRemoval[$tableName]) || !key_exists('removal', $this->tablesForRemoval[$tableName]['relations']) ) {
            return [];
        }

        return $this->tablesForRemoval[$tableName]['relations']['removal'];
    }
}