<?php

namespace Photon\PhotonCms\Core\Helpers;

use DB;
use Schema;
use Config;
use Illuminate\Support\Facades\Artisan;
use Photon\PhotonCms\Core\Entities\Seed\SeedTemplate;
use Carbon\Carbon;

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
     * Deletes all dynamic model extender files in the system.
     */
    public static function deleteModuleExtenders()
    {
        $pathToModelExtenders = app_path(Config::get('photon.dynamic_module_extenders_location'));
        self::deleteDirectoryFiles($pathToModelExtenders, '*.php');
    }

    /**
     * Deletes all asset files
     */
    public static function deleteAssets()
    {
        $pathToAssets = config('filesystems.disks.assets.root');
        self::deleteDirectoryFiles($pathToAssets, '*');
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
     * Removes all files from directories which are designated to be emptied on photon reset
     */
    public static function cleanDirectories()
    {
        $directories = config('photon.photon_reset_clean_directories');
        foreach ($directories as $directory) {
            self::deleteDirectoryFiles($directory, '*');
        }
        $pathToMigrations = base_path(Config::get('photon.dynamic_model_migrations_dir'));
        self::deleteDirectoryFiles($pathToMigrations, '*.php');
        $pathToPHPSeeds = Config::get('photon.php_seed_backup_location');
        self::deleteDirectoryFiles($pathToPHPSeeds, '*.php');
    }

    /**
     * Creates new module extender files from default module extender files from the core.
     */
    public static function rebuildDefaultModuleExtenders()
    {
        $pathToDefaultModuleExtenders = app_path('/PhotonCms/Core/DefaultCoreModuleExtensions');
        $extenders                    = glob("$pathToDefaultModuleExtenders/*.stub");
        $pathToModuleExtenders        = app_path(Config::get('photon.dynamic_module_extenders_location'));

        foreach ($extenders as $extender) {
            $extendername = basename($extender);
            $extendername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $extendername);
            app("Illuminate\Filesystem\Filesystem")->copy($extender, "$pathToModuleExtenders/$extendername.php");
        }
    }

    public static function seedInitialCore()
    {
        $seedsPath = database_path().'/seeds';
        $seeds     = glob("$seedsPath/InitialCore*.php");

        foreach ($seeds as $seed) {
            $className = basename($seed, ".php");
            Artisan::call('db:seed', ['--class' => $className, '--force' => true]);
        }
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
     * Seeds initial data for the photon installation.
     */
    public static function seedInitialValues()
    {
        $seedsPath = database_path().'/seeds';
        $seeds     = glob("$seedsPath/InitialValues*.php");

        foreach ($seeds as $seed) {
            $className = basename($seed, ".php");
            Artisan::call('db:seed', ['--class' => $className, '--force' => true]);
        }
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
     * Sets all password_created_at values to current time
     */
    public static function updatePasswordCreationTime()
    {
        $authClassName = config("auth.providers.users.model");
        $user = new $authClassName();
        $user->query()->update(['password_created_at' => Carbon::now()]);
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
        Artisan::call('migrate', ['--quiet' => true, '--force' => true]);
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

    /**
     * Removes all log files created during photon usage.
     */
    public static function removeLogFiles()
    {
        $logFiles = [
            Config::get('photon.error_log')
        ];

        foreach ($logFiles as $logFile) {
            if (file_exists($logFile)) {
                unlink($logFile);
            }
        }
    }
}