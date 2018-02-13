<?php

namespace Photon\PhotonCms\Core\Handlers\Events\Module;

Use DB;
use Schema;
Use Photon\PhotonCms\Core\Entities\Module\Module;
Use Photon\PhotonCms\Core\Entities\Field\Field;
use Photon\Events\FieldsPostrun;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MergeModulesFromBackupTable
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ModulesPostrun  $event
     * @return void
     */
    public function handle(FieldsPostrun $event)
    {
        // Loading backed up field data from before seeding
        $backedUpFields = \DB::table("temp_fields")->get();

        // Loading all modules which are in the DB after seeding (reffering to these as new)
        $allModules = Module::all();

        // Prepraring new modules (from after seeding) data for checking against old data
        $newModelNames = [];
        $newTableNames = [];
        foreach ($allModules as $module) {
            $newModelNames[] = $module->model_name;
            $newTableNames[] = $module->table_name;
        }

        // Loading backed up module data from before seeding
        $backedUpModuleData = \DB::table("temp_modules")->get();

        // Checking old module data against new. Old modules which were overwritten by seeding process will be inserted again as new modules, along with their fields
        foreach ($backedUpModuleData as $backedUpModule) {
            if (// If a module from before seeding is not overlapping with any module after seeding.
                !in_array($backedUpModule->model_name, $newModelNames) &&
                !in_array($backedUpModule->table_name, $newTableNames)
            ) {
                // Filter only fields for this module
                $newFields = array_filter($backedUpFields, function ($item) use ($backedUpModule) {
                    return $backedUpModule->id === $item->module_id;
                });

                // Create the overwritten module again
                $backedUpModule->id = null;
                $newModule = new Module((array) $backedUpModule);
                $newModule->save();

                // Attach overwritten fields again
                foreach ($newFields as $newFieldData) {
                    $newFieldData->module_id = $newModule->id;
                    $newField = new Field((array) $newFieldData);
                    $newField->save();
                }
            }
            else if (!in_array($backedUpModule->model_name, $newModelNames)) {
                // same table has been used
            }
            else if (!in_array($backedUpModule->table_name, $newTableNames)) {
                // same model name has been used
            }
        }

        Schema::dropIfExists('temp_modules');
        Schema::dropIfExists('temp_fields');
    }
}
