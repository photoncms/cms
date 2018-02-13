<?php

namespace Photon\PhotonCms\Core\Helpers;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class RelationsHelper
{

    /**
     * Generates a name for a pivot table.
     *
     * @param string $firstTableName
     * @param string $secondTableName
     * @return string
     * @throws PhotonException
     */
    public static function generatePivotTableName($firstTableName, $secondTableName)
    {
        $stringState = strcmp($firstTableName, $secondTableName);
        if ($stringState > 0) {
            return $secondTableName.'_'.$firstTableName;
        }
        elseif ($stringState < 0) {
            return $firstTableName.'_'.$secondTableName;
        }
        else {
            return $firstTableName.'_'.$secondTableName.'_pivot';
            // throw new PhotonException('PIVOT_TABLE_NAMES_THE_SAME', ['first' => $firstTableName, 'second' => $secondTableName]);
        }
    }

    /**
     * Generates a pivot table field name ftom the table name.
     *
     * @param string $tableName
     * @return string
     */
    public static function generateFieldNameFromTableName($tableName)
    {
        return str_singular($tableName).'_id';
    }
}