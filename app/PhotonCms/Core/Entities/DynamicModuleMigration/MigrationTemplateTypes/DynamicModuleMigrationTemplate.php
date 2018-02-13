<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleMigration\MigrationTemplateTypes;

use Photon\PhotonCms\Core\Entities\Migration\MigrationTemplate;
use Photon\PhotonCms\Core\Entities\ModelAttribute\ModelAttributeFactory;
use Photon\PhotonCms\Core\Entities\ModelRelation\ModelRelationFactory;

class DynamicModuleMigrationTemplate extends MigrationTemplate
{
    /**
     * Array of default fields for each module.
     * These fields are mandatory for each module in photon, and they are automatically added to the migration.
     *
     * @var array
     */
    protected $defaultFields = [];

    /**
     * Array of default relations for each module.
     * These relations are mandatory for each module in photon, and they are automatically added to the migration.
     *
     * @var array
     */
    protected $defaultRelations = [];

    public function __construct()
    {
        parent::__construct();
        $this->setPath(config('photon.dynamic_model_migrations_dir'));
    }

    public function addNewModuleTable($tableName, $fields = [], $relations = [])
    {
        $allFields = array_merge(
            $fields, ModelAttributeFactory::makeMultiple($this->defaultFields)
        );
        if (config('photon.use_slugs')) {
            $allFields[] =  ModelAttributeFactory::make([
                'name' => 'slug',
                'field_type' => 13,
                'laravel_type' => 'string',
                'parameters' => [255],
                'indexed' => true
            ]);
        }

        $allRelations = array_merge(
            $relations, ModelRelationFactory::makeMultipleFromFieldDataArray($this->defaultRelations, $tableName)
        );
        
        $this->addTableForCreation($tableName, $allFields, $allRelations);
    }
}