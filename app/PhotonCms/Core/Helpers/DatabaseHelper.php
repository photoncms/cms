<?php

namespace Photon\PhotonCms\Core\Helpers;

use Photon\PhotonCms\Core\Helpers\StringConversionsHelper;

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