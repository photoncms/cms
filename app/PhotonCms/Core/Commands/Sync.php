<?php

namespace Photon\PhotonCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
        $this->line('Data backup');

        ResetHelper::deleteModels();
        $this->line('Models deleted');

        ResetHelper::deleteMigrations();
        $this->line('Migrations deleted');

        ResetHelper::deleteTables();
        $this->line('Tables deleted');

        ResetHelper::runMigrations(); // Runs photon base migrations
        $this->line('Run base migrations');

        DatabaseHelper::seedTablesData(config('photon.photon_sync_clear_tables'), true);
        $this->line('Run dynamic module seeders');

        ResetHelper::rebuildAndRunMigrations(); // Re/builds all photon module migrations and runs them
        $this->line('Rebuild and run migrations');

        ResetHelper::rebuildModels();
        $this->line('Rebuild models');

        foreach ($backedUpTableNames as $backedUpTableName) {
            $gateway = $this->dynamicModuleLibrary->getGatewayInstanceByTableName($backedUpTableName);
            $this->dynamicModuleRepository->restoreModuleData($gateway);
        }
        $this->line('Restore modules data');

        foreach ($backedUpPivotTables as $moduleNameKey => $backedUpPivotTable) {
            foreach ($backedUpPivotTable as $individualTable) {
                $gateway = $this->dynamicModuleLibrary->getGatewayInstanceByTableName($moduleNameKey);
                $this->dynamicModuleRepository->restorePivotTableData($individualTable, $gateway);
            }
        }
        $this->line('Restore pivot tables data');

        $this->info("Photon CMS Synced");
    }
}
