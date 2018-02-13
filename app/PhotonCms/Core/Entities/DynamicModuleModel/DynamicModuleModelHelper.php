<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleModel;

/**
 * Contains DynamicModuleModel entity helper functions.
 *
 * Functionalities which are directly related to the DynamicModuleModel entity, but aren't performed over DynamicModuleModel instances.
 */
class DynamicModuleModelHelper
{

    /**
     * Creates upper-camelcase notation model name from the passed string.
     * Uses a regular expression to match any aplhabet letters (case insensitive).
     *
     * @param string $string
     * @return string
     */
    public static function generateModelNameFromString($string)
    {
        preg_match_all("/[a-z0-9]+/i", $string, $matches);

        // ToDo: Needs handling for no matches (Sasa|01/2016)
        $modelName = '';
        foreach ($matches[0] as $match) {
            $modelName .= ucfirst(strtolower($match));
        }

        return $modelName;
    }

    /**
     * Uses generateModelNameFromString() to generate the model extender name and suffixes it with 'ModuleExtensions'.
     *
     * @param string $string
     * @return string
     */
    public static function generateModelExtenderNameFromString($string)
    {
        return self::generateModelNameFromString($string).'ModuleExtensions';
    }
}