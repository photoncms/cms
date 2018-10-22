<?php

namespace Photon\PhotonCms\Core\Helpers;

use Photon\PhotonCms\Core\Helpers\StringConversionsHelper;
use Photon\PhotonCms\Core\Entities\Seed\SeedTemplate;

class DatabaseHelper
{

    /**
     * Removes all data from specified tables.
     *
     * @param array $tableNames
     * @param boolean $force
     */
    public static function emptyTables(array $tableNames, $force = false)
    {
        if ($force) { \Schema::disableForeignKeyConstraints(); }
        foreach ($tableNames as $tableName) {
            self::emptyTable($tableName);
        }
        if ($force) { \Schema::enableForeignKeyConstraints(); }
    }

    /**
     * Removes all data from the specified table.
     *
     * @param string $tableName
     * @param boolean $force
     */
    public static function emptyTable($tableName, $force = false)
    {
        if ($force) { \Schema::disableForeignKeyConstraints(); }
        \DB::table($tableName)->delete();
        if ($force) { \Schema::enableForeignKeyConstraints(); }
    }

    /**
     * Runs seeders for all specified tables.
     *
     * @param array $tableNames
     * @param boolean $force
     */
    public static function seedTablesData(array $tableNames, $force = false)
    {
        if ($force) { \Schema::disableForeignKeyConstraints(); }
        foreach ($tableNames as $tableName) {
            self::seedTableData($tableName);
        }
        if ($force) { \Schema::enableForeignKeyConstraints(); }
    }

    /**
     * Runs seeder for the specified table.
     *
     * @param string $tableName
     * @param boolean $force
     */
    public static function seedTableData($tableName, $force = false)
    {
        if ($force) { \Schema::disableForeignKeyConstraints(); }
        $seedName = StringConversionsHelper::snakeCaseToCamelCase($tableName);
        \Artisan::call('db:seed', ['--class' => $seedName.'TableSeeder', '--force' => true]);
        if ($force) { \Schema::enableForeignKeyConstraints(); }
    }

    /**
     * Rebuilds module seeders.
     */
    public static function rebuildSeeders()
    {
        // ToDo: needs a SeedTemplateFactory here (Sasa|01/2016)
        $seedTemplate = new SeedTemplate();
        $seedTemplate->addTable('modules');
        $seedTemplate->addTable('field_types');
        $seedTemplate->addTable('model_meta_types');
        $seedTemplate->addTable('field_groups');
        $seedTemplate->useForce();
        app('Photon\PhotonCms\Core\Entities\Seed\SeedRepository')->create(
            $seedTemplate, 
            app('Photon\PhotonCms\Core\Entities\Seed\SeedGateway')
        );

        $seedTemplate = new SeedTemplate();
        $seedTemplate->addTable('fields');
        $seedTemplate->addTable('model_meta_data');
        $seedTemplate->addExclusion('id');
        $seedTemplate->useForce();
        app('Photon\PhotonCms\Core\Entities\Seed\SeedRepository')->create(
            $seedTemplate, 
            app('Photon\PhotonCms\Core\Entities\Seed\SeedGateway')
        );
    }    

    /**
     * Runs migrations.
     * If the path parameter is specified, migrations will be executed at that location.
     *
     * @param string $path
     * @return boolean
     */
    public static function runMigrations($path = null)
    {
        $parameters = [];
        if ($path) {
            $parameters['--path'] = $path;
        }

        $parameters['--force'] = true;

        return \Artisan::call('migrate', $parameters);
    }
}