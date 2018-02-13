<?php

namespace Photon\PhotonCms\Core\Entities\Seed;

use Photon\PhotonCms\Core\Entities\Seed\Contracts\SeedTemplateInterface;

class SeedTemplate implements SeedTemplateInterface
{
    /**
     * Array of table names which for whom seeds will be made.
     *
     * @var array
     */
    private $tables = [];

    /**
     * Flag for force overwriting existing seeds.
     *
     * @var boolean
     */
    private $force = false;

    /**
     * Flag for cleaning of the DatabaseSeeder.php before creating new seeds.
     *
     * @var boolean
     */
    private $clean = false;

    /**
     * Name of the database on which the seed will be performed
     *
     * @var string
     */
    private $databaseName = '';

    /**
     * Maximum number of entries from each table for seeding.
     *
     * @var int
     */
    private $max = 0;

    /**
     * Seed prerun event names.
     *
     * @var array
     */
    private $preruns = [];

    /**
     * Seed postrun event names.
     *
     * @var array
     */
    private $postruns = [];

    /**
     * Seed exclusion fields
     *
     * @var array
     */
    private $exclusions = [];

    /**
     * Indicates if the seeder should have values indexed.
     *
     * @var boolean
     */
    private $indexed = false;

    /**
     * Returns an array of table names which will be used in seed creation.
     *
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Adds a table name to the array of table names for seeding.
     * Optionally registers a prerun and/or postrun event name.
     *
     * @param string $tableName
     * @param string $prerun
     * @param string $postrun
     */
    public function addTable($tableName, $prerun = null, $postrun = null)
    {
        $this->tables[] = $tableName;

        if ($prerun) {
            $this->preruns[$tableName] = $prerun;
        }

        if ($postrun) {
            $this->postruns[$tableName] = $postrun;
        }
    }

    /**
     * Checks if force overwriting is switched on.
     *
     * @return boolean
     */
    public function usesForce()
    {
        return $this->force;
    }

    /**
     * Switches force overwriting to on.
     */
    public function useForce()
    {
        $this->force = true;
    }

    /**
     * Checks if cleaning of the DatabaseSeeder.php file cleaning is switched on before seed generation.
     *
     * @return boolean
     */
    public function usesClean()
    {
        return $this->clean;
    }

    /**
     * Switches on cleaning for DatabaseSeeder.php before seed generation.
     */
    public function useClean()
    {
        $this->clean = true;
    }

    /**
     * Returns a database namewhich is set for the seed.
     * If not set, default name will be used from Laravel configuration.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Sets the database name over which seeds will be executed.
     *
     * @param string $databaseName
     */
    public function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
    }

    /**
     * Check if specific database name was specified.
     *
     * @return boolean
     */
    public function hasDatabaseName()
    {
        return $this->databaseName !== '';
    }

    /**
     * Returns the number of maximum entries to be seeded from the DB.
     * 0 means no limit.
     *
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Checks if the maximum number of seeded entries per table was set.
     *
     * @return boolean
     */
    public function usesMax()
    {
        return $this->max > 0;
    }

    /**
     * Sets the maximum number of entries to be seeded per table.
     *
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * Returns an array of all prerun event names mapped with their respective table names.
     *
     * @return array
     */
    public function getPreruns()
    {
        return $this->preruns;
    }

    /**
     * Checks if any prerun events have been set for this seed.
     *
     * @return boolean
     */
    public function hasPreruns()
    {
        return !empty($this->preruns);
    }

    /**
     * Returns an array of all postrun event names mapped with their respective table names.
     *
     * @return array
     */
    public function getPostruns()
    {
        return $this->postruns;
    }

    /**
     * Checks if any postrun events have been set for this seed.
     *
     * @return boolean
     */
    public function hasPostruns()
    {
        return !empty($this->postruns);
    }

    /**
     * Returns an array of all field names which should be excluded from the seed.
     *
     * @return array
     */
    public function getExclusions()
    {
        return $this->exclusions;
    }

    /**
     * Sets an array of all field names which should be excluded from the seed.
     *
     * @param array $exclusions
     */
    public function setExclusions(array $exclusions)
    {
        $this->exclusions = $exclusions;
    }

    /**
     * Adds a field name for exclusion from the seed.
     *
     * @param string $fieldName
     */
    public function addExclusion($fieldName)
    {
        $this->exclusions[] = $fieldName;
    }

    /**
     * Checks if there are fields which should be excluded from the seed.
     *
     * @return boolean
     */
    public function hasExclusions()
    {
        return !empty($this->exclusions);
    }

    /**
     * Indicates if the seeded values should be indexed.
     *
     * @return boolean
     */
    public function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * Switches seeded values indexing on.
     *
     * @param boolean $indexed
     */
    public function indexingOn($indexed)
    {
        $this->indexed = $indexed;
    }

    /**
     * Switches seeded values indexing off.
     *
     * @param boolean $indexed
     */
    public function indexingOff($indexed)
    {
        $this->indexed = $indexed;
    }
}