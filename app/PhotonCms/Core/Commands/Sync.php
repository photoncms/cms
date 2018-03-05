<?php

namespace Photon\PhotonCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Schema;

use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleRepository;
use Photon\PhotonCms\Core\Entities\ModelRelation\ModelRelationFactory;
use Photon\PhotonCms\Core\Helpers\ResetHelper;
use Photon\PhotonCms\Core\Helpers\DatabaseHelper;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photon:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Performs the Photon Sync action';

    /**
     *
     * @var ModelRepository
     */
    private $modelRepository;

    /**
     *
     * @var ModuleGatewayInterface
     */
    private $moduleGateway;

    /**
     * @var DynamicModuleLibrary
     */
    private $dynamicModuleLibrary;

    /**
     * @var DynamicModuleRepository
     */
    private $dynamicModuleRepository;

    /**
     * Create a new command instance.
     *
     * @param ModelRepository $modelRepository
     * @param ModuleGatewayInterface $moduleGateway
     * @param DynamicModuleLibrary $dynamicModuleLibrary
     * @param DynamicModuleRepository $dynamicModuleRepository
     * @return void
     */
    public function __construct(
        ModuleRepository $moduleRepository,
        ModuleGatewayInterface $moduleGateway,
        DynamicModuleLibrary $dynamicModuleLibrary,
        DynamicModuleRepository $dynamicModuleRepository
    ) {
        $this->moduleRepository        = $moduleRepository;
        $this->moduleGateway           = $moduleGateway;
        $this->dynamicModuleLibrary    = $dynamicModuleLibrary;
        $this->dynamicModuleRepository = $dynamicModuleRepository;
        parent::__construct();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // clear cache
        if(config("photon.use_photon_cache")) {
            Cache::tags(env("APPLICATION_URL"))->flush();
        }
        Cache::flush("all_permissions");

        $isEmptyDb = !(Schema::hasTable('modules') && Schema::hasTable('fields'));

        // back up data if db is set
        if(!$isEmptyDb) {
            $modules = $this->moduleRepository->getAll($this->moduleGateway);

            $backedUpTableNames = [];
            $backedUpPivotTables = [];
            foreach ($modules as $module) {
                $gateway = $this->dynamicModuleLibrary->getGatewayInstanceByTableName($module->table_name);
                $this->dynamicModuleRepository->backupModuleData($gateway);
                $backedUpTableNames[] = $module->table_name;

                $modelRelations = ModelRelationFactory::makeMultipleFromFields($module->fields);
                foreach ($modelRelations as $relation) {
                    if(!$relation->requiresPivot()) {
                        continue;
                    }

                    $gateway = $this->dynamicModuleLibrary->getGatewayInstanceByTableName($module->table_name);
                    $this->dynamicModuleRepository->backupPivotTableData($relation, $gateway);

                    $backedUpPivotTables[$module->table_name][] = $relation->pivotTable;
                }
            }
            $this->info('...Data backup performed');

            $this->dynamicModuleRepository->backupSystemTables($gateway);
            $this->info('...System Tables backed up');
        }


        ResetHelper::deleteModels();
        $this->info('...Models removed');

        ResetHelper::deleteMigrations();
        $this->info('...Migrations removed');

        ResetHelper::deleteTables();
        $this->info('...Tables removed');

        ResetHelper::runMigrations();
        $this->info('...Base migrations ran');

        DatabaseHelper::seedTablesData(config('photon.photon_sync_clear_tables'), true);
        $this->info('...Dynamic module seeders ran');

        ResetHelper::rebuildAndRunMigrations();
        $this->info('...Migrations rebuilt and ran');

        ResetHelper::rebuildModels();
        $this->info('...Models rebuilt');

        if(!$isEmptyDb) {
            $modules = $this->moduleRepository->getAll($this->moduleGateway);
            foreach ($modules as $module) {
                $gateway = $this->dynamicModuleLibrary->getGatewayInstanceByTableName($module->table_name);
                $this->dynamicModuleRepository->restoreModuleData($gateway);

                $modelRelations = ModelRelationFactory::makeMultipleFromFields($module->fields);
                foreach ($modelRelations as $relation) {
                    if(!$relation->requiresPivot()) {
                        continue;
                    }
                    
                    $this->dynamicModuleRepository->restorePivotTableData($relation->pivotTable, $gateway);
                }
            }
            $this->info('...Modules data restored');

            $this->dynamicModuleRepository->restoreSystemTables($gateway);
            $this->info('...System Tables restored');
        } else {
            ResetHelper::seedInitialValues();
            $this->info('...Initial Values seeded');
        }

        $this->info("Photon CMS Synced");
    }
}
