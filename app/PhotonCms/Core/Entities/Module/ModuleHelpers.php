<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Module\Module;
// use Photon\PhotonCms\Dependencies\Traits\AnchorFields;

/**
 * Contains Module entity helper functions.
 *
 * Functionalities which are directly related to the Module entity, but aren't performed over Module instances.
 */
class ModuleHelpers
{

    /**
     * Validates the table name.
     *
     * @param string $tableName
     * @return string
     * @throws PhotonException
     */
    public static function validateTableName($tableName)
    {
        preg_match_all("/[^a-zA-Z0-9_]/", $tableName, $matches);

        if (!empty($matches[0])) {
            throw new PhotonException('BAD_TABLE_NAME', ['invalid_characters' => array_values(array_unique($matches[0]))]);
        }

        return $tableName;
    }

    /**
     * Validates anchor text against field names.
     *
     * @param string $anchorText
     * @param array $fieldNames
     * @throws PhotonException
     */
    public static function validateAnchorTextAgainstFieldNames($anchorText, array $fieldNames)
    {
        // Small hack to allow usage of 'id' field as anchor text. Assuming that any module model will have an ID field.
        $fieldNames['id'] = null;
        preg_match_all("/{{([^{}]+)}}/", $anchorText, $anchorUsedFields);

        if (!empty($anchorUsedFields[1])) {
            $excessFields = [];
            foreach ($anchorUsedFields[1] as $usedField) {
                $anchorTextItems = explode('.', $usedField);

                if (!self::checkArrayStructureByItemStack($fieldNames, $anchorTextItems)) {
                    $excessFields[] = $usedField;
                }
            }

            // if there are no excess fields anchor is valid
            if(empty($excessFields))
                return true;

            // check for anchor methods
            foreach ($excessFields as $key => $excessField) {
                $functionData = explode("|", $excessField);
                // if method is not formated properly throw error
                if(count($functionData) != 2)
                    throw new PhotonException('TRYING_TO_USE_NON_EXISTING_FIELD_AS_ANCHOR_TEXT', ['fields' => $excessFields]);

                // if method does not exist in trait throw error
                $functionName = $functionData[1];
                if(!method_exists('Photon\PhotonCms\Dependencies\Traits\AnchorFields', $functionName)) 
                    throw new PhotonException('TRYING_TO_USE_NON_EXISTING_FIELD_AS_ANCHOR_TEXT', ['fields' => $excessFields]);
                
                unset($excessFields[$key]);
            }

            if (!empty($excessFields)) {
                throw new PhotonException('TRYING_TO_USE_NON_EXISTING_FIELD_AS_ANCHOR_TEXT', ['fields' => $excessFields]);
            }
        }
    }

    /**
     * Checks an array structure by items from another array
     *
     * @param array $array
     * @param array $items
     * @return boolean
     */
    private static function checkArrayStructureByItemStack($array, array $items)
    {
        if (!empty($items) && array_key_exists($items[0], $array)) {
            $subarray = $array[$items[0]];
            array_splice($items, 0, 1);
            if (empty($items) && !$subarray) {
                return true;
            }
            else {
                return self::checkArrayStructureByItemStack($subarray, $items);
            }
        }

        return false;
    }

    /**
     * Checks if the given module implements node functionality.
     *
     * @param Module $module
     * @return boolean
     */
    public static function checkIfNodeModule(Module $module)
    {
        $nodeModuleypes = ['sortable', 'multilevel_sortable'];

        return in_array($module->type, $nodeModuleypes);
    }
}