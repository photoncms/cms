<?php

namespace Photon\PhotonCms\Core\Handlers\Events\Module;

Use DB;
Use Schema;
use Illuminate\Database\Schema\Blueprint;
use Photon\Events\ModulesPrerun;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BackupModulesToTemporaryTable
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
     * @param  ModulesPrerun  $event
     * @return void
     */
    public function handle(ModulesPrerun $event)
    {
        $this->createModulesTemporaryTable();
        $this->backUpTable('modules');
        $this->createFieldsTemporaryTable();
        $this->backUpTable('fields');
    }

    /**
     * Creates a temporary table and backs up data from th requested table.
     *
     * @param string $tableName
     */
    private function backUpTable($tableName)
    {
        $temporaryTableName = "temp_$tableName";

        $moduleData = \DB::table($tableName)->get();

        array_walk($moduleData, function (&$entry) {
            if (is_object($entry)) {
                $entry = (array) $entry;
            }
        });

        \DB::table($temporaryTableName)->insert($moduleData);
    }

    /**
     * Creates a temporary table for modules.
     * Actions in this function must comply with photon's migration for modules table.
     */
    private function createModulesTemporaryTable()
    {
        Schema::dropIfExists('temp_modules');

        Schema::create('temp_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lft');
            $table->integer('rgt');
            $table->integer('parent_id')->nullable()->default(null);
            $table->integer('depth')->default(0);
            $table->integer('category')->nullable()->default(null);
            $table->string('type');
            $table->string('name');
            $table->string('model_name');
            $table->string('table_name')->unique();
            $table->string('anchor_text')->nullable->default(null);
            $table->string('anchor_html')->nullable->default(null);
            $table->string('icon')->nullable()->default(null);
            $table->boolean('reporting')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Creates a temporary table for fields.
     * Actions in this function must comply with photon's migration for fields table.
     */
    private function createFieldsTemporaryTable()
    {
        Schema::dropIfExists('temp_fields');

        Schema::create('temp_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->string('name');
            $table->integer('related_module')->nullable()->default(null);
            $table->string('relation_name')->nullable()->default(null);
            $table->string('pivot_table')->nullable()->default(null);
            $table->string('column_name')->nullable()->default(null);
            $table->string('tooltip_text')->nullable()->default(null);
            $table->string('validation_rules')->nullable()->default(null);
            $table->integer('module_id')->unsigned()->index();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
}
