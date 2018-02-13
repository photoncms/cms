<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use Photon\PhotonCms\Core\Helpers\CodeHelper;

/**
 * Handles object manipulation.
 */
class ModuleFactory
{

    /**
     * Makes an instance of a Module.
     *
     * @param array $data
     * @return \Photon\PhotonCms\Core\Entities\Module\Module
     */
    public static function make($data = [])
    {
        if (!isset($data['id'])) {
            $data['id'] = CodeHelper::generateModuleUID();
        }
        return new Module($data);
    }

    /**
     *
     * Makes an empty instance of an ORM Module.
     *
     * This should never be persisted!
     *
     * @return \Photon\PhotonCms\Core\Entities\Module\Module
     */
    public static function makeEmpty()
    {
        $emptyModule = new Module();
        $emptyModule->isEmpty = true;
        
        return $emptyModule;
    }
    
    /**
     * Replaces Module data from an array.
     *
     * @param \Photon\PhotonCms\Core\Entities\Module\Module $module
     * @param array $data
     */
    public static function replaceData(Module $module, array $data)
    {
        $attributes = [
            'id',
            'category',
            'type',
            'name',
            'model_name',
            'table_name',
            'anchor_text',
            'anchor_html',
            'icon',
            'reporting',
            'created_at',
            'updated_at'
        ];

        foreach ($data as $propertyName => $value) {
            if (in_array($propertyName, $attributes)) {
                $module->$propertyName = $value;
            }
        }
    }
}