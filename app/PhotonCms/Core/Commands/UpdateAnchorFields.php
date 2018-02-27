<?php

namespace Photon\PhotonCms\Core\Commands;

use Illuminate\Console\Command;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary;
use Photon\PhotonCms\Core\Entities\Module\ModuleLibrary;
use Photon\PhotonCms\Core\IAPI\IAPI;

class UpdateAnchorFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photon:update-anchor-fields {tables?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all anchor_text and anchor_html fields for either all or specific modules';

    /**
     * @var ModuleLibrary
     */
    private $moduleLibrary;

    /**
     * @var DynamicModuleLibrary
     */
    private $dynamicModuleLibrary;

    /**
     * @var IAPI
     */
    private $iapi;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
    	ModuleLibrary $moduleLibrary,
    	DynamicModuleLibrary $dynamicModuleLibrary,
    	IAPI $iapi
    ) {
    	$this->moduleLibrary 			= $moduleLibrary;
        $this->dynamicModuleLibrary     = $dynamicModuleLibrary;
        $this->iapi 					= $iapi;

        parent::__construct();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('tables', InputArgument::OPTIONAL, 'comma separated string of table names'),
        );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->iapi->loginIapiUserIfNotLoggedIn();

    	// retrieve tables argument
        $tables = $this->argument('tables');
        if($tables)
        	$tables = explode(",", $tables);
        else
        	$tables = [];

        // prepare modules
        $modules = [];
        if(!$tables)
        	$modules = $this->moduleLibrary->getAllModules();
        else {
        	foreach ($tables as $tableName) {
        		$module = $this->moduleLibrary->findByTableName($tableName);
        		if(!$module)
        			continue;
        		$modules[] = $module;
        	}
        }

        foreach ($modules as $key => $module) {
        	$this->dynamicModuleLibrary->updateAllModuleAnchorTextsForEntries($module, "anchor_text");
        	$this->dynamicModuleLibrary->updateAllModuleAnchorTextsForEntries($module, "anchor_html");
        }

        $this->info("Anchor fields for " . count($modules) . " modules successfully updated.");
        return false;
    }
}
