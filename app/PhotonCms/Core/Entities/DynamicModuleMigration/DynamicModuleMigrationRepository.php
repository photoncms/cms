<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleMigration;

use Photon\PhotonCms\Core\Entities\Migration\MigrationRepository;
use Photon\PhotonCms\Core\Entities\Module\Module;
use Photon\PhotonCms\Core\Entities\ModelRelation\ModelRelationFactory;
use Photon\PhotonCms\Core\Entities\ModelAttribute\ModelAttributeFactory;
use Photon\PhotonCms\Core\Entities\DynamicModuleMigration\DynamicModuleMigrationFactory;
use Photon\PhotonCms\Core\Helpers\MigrationsHelper;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Module\ModuleLibrary;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationCompilerInterface;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationGatewayInterface;

class DynamicModuleMigrationRepository extends MigrationRepository
{
    private $moduleLibrary;

    public function __construct(
        ModuleLibrary $moduleLibrary
    )
    {
        $this->moduleLibrary = $moduleLibrary;
    }

    public function rebuildModelMigration(Module $module, MigrationCompilerInterface $compiler, MigrationGatewayInterface $gateway)
    {
        // Model relations preparation
        $modelRelations = ModelRelationFactory::makeMultipleFromFields($module->fields);
        // Model attributes preparation
        $modelAttributes = ModelAttributeFactory::makeMultipleFromFields($module->fields);
        // Prepare migration template
        $migrationTemplate = DynamicModuleMigrationFactory::makeByType($module->type);
        $migrationTemplate->setClassName(MigrationsHelper::generateAutoMigrationClassName());
        $migrationTemplate->addNewModuleTable($module->table_name, $modelAttributes, $modelRelations);

        if (!$this->create($migrationTemplate, $compiler, $gateway)) {
            throw new PhotonException('CANNOT_MAKE_MIGRATION_TEMPLATE_FOR_TYPE', ['module_id' => $module->id, 'type' => $module->type]);
        }
    }

    public function rebuildAllModelMigrations(MigrationCompilerInterface $compiler, MigrationGatewayInterface $gateway)
    {
        $modules = $this->moduleLibrary->getAllModules();

        $allModelRelations = [];
        foreach ($modules as $module) {
            // Model relations preparation
            $allModelRelations[$module->table_name] = ModelRelationFactory::makeMultipleFromFields($module->fields);
            // Model attributes preparation
            $modelAttributes = ModelAttributeFactory::makeMultipleFromFields($module->fields);
            // Prepare migration template
            $migrationTemplate = DynamicModuleMigrationFactory::makeByType($module->type);
            $migrationTemplate->setClassName(MigrationsHelper::generateAutoMigrationClassName());
            $migrationTemplate->addNewModuleTable($module->table_name, $modelAttributes);

            if (!$this->create($migrationTemplate, $compiler, $gateway)) {
                throw new PhotonException('CANNOT_MAKE_MIGRATION_TEMPLATE_FOR_TYPE', ['module_id' => $module->id, 'type' => $module->type]);
            }


        }

        if (!empty($allModelRelations)) {
            // Prepare all relations migration template
            $migrationTemplate = DynamicModuleMigrationFactory::make();
            $migrationTemplate->setClassName(MigrationsHelper::generateAutoMigrationClassName());

            foreach ($allModelRelations as $tableName => $relations) {
                $migrationTemplate->addTableForUpdateRelationsForCreation($tableName, $relations);
            }

            if (!$this->create($migrationTemplate, $compiler, $gateway)) {
                throw new PhotonException('CANNOT_MAKE_MIGRATION_TEMPLATE_FOR_ALL_RELATIONS');
            }
        }

        return DynamicModuleMigrationGateway::runModelMigrations();
    }
}