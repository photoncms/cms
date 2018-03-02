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

class SoftReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photon:soft-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Performs the Photon Soft Reset action';

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
        if (!env('CAN_RESET_PHOTON') || !\App::environment('local', 'staging', 'development', 'testing')) {
            $this->info('...Photon reset forbidden');
            return false;
        }

        // clear cache
        if(config("photon.use_photon_cache")) {
            Cache::tags(env("APPLICATION_URL"))->flush();
        }
        Cache::flush("all_permissions");

        // does not belong in sync
        ResetHelper::removeLogFiles();
        $this->info('...Log files removed');

        ResetHelper::deleteModels();
        $this->info('...Models removed');

        ResetHelper::deleteAssets();
        $this->info('...Assets removed');

        ResetHelper::deleteMigrations();
        $this->info('...Migrations removed');

        ResetHelper::deleteTables();
        $this->info('...Tables removed');

        ResetHelper::cleanDirectories();
        $this->info('...Directories cleared');

        ResetHelper::runMigrations();
        $this->info('...Base migrations ran');

        ResetHelper::seedInitialCore();
        $this->info('...Initial Core seeded');

        ResetHelper::rebuildAndRunMigrations();
        $this->info('...Migrations rebuilt and ran');

        ResetHelper::seedInitialValues();
        $this->info('...Initial Values seeded');

        ResetHelper::rebuildModels();
        $this->info('...Models rebuilt');

        ResetHelper::rebuildSeeders();
        $this->info('...Seeders rebuilt');

        ResetHelper::updatePasswordCreationTime();
        $this->info('...Password Creation Time updated');

        $this->info("Photon CMS Soft Reseted");
    }
}
