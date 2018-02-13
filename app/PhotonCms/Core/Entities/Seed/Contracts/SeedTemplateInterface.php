<?php

namespace Photon\PhotonCms\Core\Entities\Seed\Contracts;

interface SeedTemplateInterface
{

    /**
     * Returns an array of table names which will be used in seed creation.
     *
     * @return array
     */
    public function getTables();

    /**
     * Adds a table name to the array of table names for seeding.
     * Optionally registers a prerun and/or postrun event name.
     *
     * @param string $tableName
     * @param string $prerun
     * @param string $postrun
     */
    public function addTable($tableName, $prerun = null, $postrun = null);

    /**
     * Checks if force overwriting is switched on.
     *
     * @return boolean
     */
    public function usesForce();

    /**
     * Switches force overwriting to on.
     */
    public function useForce();

    /**
     * Checks if cleaning of the DatabaseSeeder.php file cleaning is switched on before seed generation.
     *
     * @return boolean
     */
    public function usesClean();

    /**
     * Switches on cleaning for DatabaseSeeder.php before seed generation.
     */
    public function useClean();

    /**
     * Returns a database namewhich is set for the seed.
     * If not set, default name will be used from Laravel configuration.
     *
     * @return string
     */
    public function getDatabaseName();

    /**
     * Sets the database name over which seeds will be executed.
     *
     * @param string $databaseName
     */
    public function setDatabaseName($databaseName);

    /**
     * Check if specific database name was specified.
     *
     * @return boolean
     */
    public function hasDatabaseName ();

    /**
     * Returns the number of maximum entries to be seeded from the DB.
     * 0 means no limit.
     *
     * @return int
     */
    public function getMax();

    /**
     * Checks if the maximum number of seeded entries per table was set.
     *
     * @return boolean
     */
    public function usesMax();

    /**
     * Sets the maximum number of entries to be seeded per table.
     *
     * @param int $max
     */
    public function setMax($max);

    /**
     * Returns an array of all prerun event names mapped with their respective table names.
     *
     * @return array
     */
    public function getPreruns();

    /**
     * Checks if any prerun events have been set for this seed.
     *
     * @return boolean
     */
    public function hasPreruns();

    /**
     * Returns an array of all postrun event names mapped with their respective table names.
     *
     * @return array
     */
    public function getPostruns();

    /**
     * Checks if any postrun events have been set for this seed.
     *
     * @return boolean
     */
    public function hasPostruns();

    /**
     * Returns an array of all field names which should be excluded from the seed.
     *
     * @return array
     */
    public function getExclusions();

    /**
     * Sets an array of all field names which should be excluded from the seed.
     *
     * @param array $exclusions
     */
    public function setExclusions(array $exclusions);

    /**
     * Adds a field name for exclusion from the seed.
     *
     * @param string $fieldName
     */
    public function addExclusion($fieldName);

    /**
     * Checks if there are fields which should be excluded from the seed.
     *
     * @return boolean
     */
    public function hasExclusions();

    /**
     * Indicates if the seeded values should be indexed.
     *
     * @return boolean
     */
    public function isIndexed();

    /**
     * Switches seeded values indexing on.
     *
     * @param boolean $indexed
     */
    public function indexingOn($indexed);

    /**
     * Switches seeded values indexing off.
     *
     * @param boolean $indexed
     */
    public function indexingOff($indexed);
}