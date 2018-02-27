<?php

namespace Photon\PhotonCms\Core\Helpers;

use DB;
use Schema;
use Config;
use Illuminate\Support\Facades\Artisan;

class ResetHelper
{

    /**
     * Deletes all dynamic model files in the system.
     */
    public static function deleteModels()
    {
        $pathToModels = app_path(Config::get('photon.dynamic_models_location'));
        self::deleteDirectoryFiles($pathToModels, '*.php');
    }

    /**
     * Deletes all dynamic migration files in the system.
     */
    public static function deleteMigrations()
    {
        $pathToMigrations = base_path(Config::get('photon.dynamic_model_migrations_dir'));
        self::deleteDirectoryFiles($pathToMigrations, '*.php');
    }

    /**
     * Loops through the specified directory and removes all files that match the specified filename expression.
     *
     * @param string $directoryPath
     * @param string $filenameExpression
     */
    private static function deleteDirectoryFiles($directoryPath, $filenameExpression = '*')
    {
        foreach (glob("$directoryPath/$filenameExpression") as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Deletes all tables in the DB.
     */
    public static function deleteTables()
    {
        $tableNames = DB::select('SHOW TABLES');
        Schema::disableForeignKeyConstraints();
        foreach ($tableNames as $tableKey => $tableName) {
            foreach ((array) $tableName as $name) {
                Schema::dropIfExists($name);
            }
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Runs all available migrations.
     */
    public static function runMigrations()
    {
        Artisan::call('migrate', ['--quiet' => true]);
    }

    /**
     * Rebuild all migrations.
     */
    public static function rebuildAndRunMigrations()
    {
        app('Photon\PhotonCms\Core\Entities\DynamicModuleMigration\DynamicModuleMigrationRepository')->rebuildAllModelMigrations(
            app('Photon\PhotonCms\Core\Entities\Migration\MigrationCompiler'), 
            app('Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationGatewayInterface')
        );
    }

    /**
     * Rebuild all models.
     */
    public static function rebuildModels()
    {
        app('Photon\PhotonCms\Core\Entities\Model\ModelRepository')->rebuildAllModels(
            app('Photon\PhotonCms\Core\Entities\Model\ModelCompiler'), 
            app('Photon\PhotonCms\Core\Entities\Model\Contracts\ModelGatewayInterface')
        );
    }
}