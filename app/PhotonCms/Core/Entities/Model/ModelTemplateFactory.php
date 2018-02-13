<?php

namespace Photon\PhotonCms\Core\Entities\Model;

use Photon\PhotonCms\Core\Entities\DynamicModuleModel\ModelTemplateTypes\DynamicModuleModelTemplate;
use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassTemplate;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Handles object manipulation.
 */
class ModelTemplateFactory
{
    /**
     * Makes an instance of a ModelTemplate.
     *
     * @return \Photon\PhotonCms\Core\Entities\Model\ModelTemplate
     */
    public static function make()
    {
        $modelTemplate = new ModelTemplate();
        
        return $modelTemplate;
    }

    /**
     * Generates a specific model template instance by specified type.
     *
     * @param string $type
     * @return \Photon\PhotonCms\Core\Entities\DynamicModuleModel\ModelTemplateTypes\DynamicModuleModelTemplate
     * @throws PhotonException
     */
    public static function makeByType($type)
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));

        $class = '\\'.config('photon.dynamic_model_templates_namespace').'\\'.$className.'Template';

        if (!class_exists($class)) {
            throw new PhotonException('CANNOT_MAKE_MODEL_TEMPLATE_FOR_TYPE', ['type' => $type]);
        }

        return new $class();
    }

    /**
     * Makes an instance of a model template for a photon module.
     *
     * @return \Photon\PhotonCms\Core\Entities\DynamicModuleModel\ModelTemplateTypes\DynamicModuleModelTemplate
     */
    public static function makeDynamicModuleModelTemplate()
    {
        $modelTemplate = new DynamicModuleModelTemplate();

        return $modelTemplate;
    }

    /**
     * Makes a new properly initialized instance of a module extension template.
     *
     * @return NativeClassTemplate
     */
    public static function makeDynamicModuleExtensionTemplate()
    {
        $extenderTemplate = new NativeClassTemplate();
        $extenderTemplate->setPath(app_path(config('photon.dynamic_module_extenders_location')));
        $extenderTemplate->setNamespace(config('photon.dynamic_module_extenders_namespace'));
        $extenderTemplate->setInheritance('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension');

        return $extenderTemplate;
    }
}