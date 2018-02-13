<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleMigration;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\DynamicModuleMigration\MigrationTemplateTypes\DynamicModuleMigrationTemplate;

class DynamicModuleMigrationFactory
{
    /**
     * Dynamic module migration templates path.
     * ToDo: move this to the photon config file (Sasa|03/2016)
     *
     * @var string
     */
    private static $dynamicModuleMigrationTemplatesPath = '\\Photon\\PhotonCms\\Core\\Entities\\DynamicModuleMigration\\MigrationTemplateTypes\\';

    public static function make()
    {
        return new DynamicModuleMigrationTemplate();
    }

    /**
     * Makes an instance of a migration template using module type name.
     *
     * @param string $type
     * @return \Photon\PhotonCms\Core\Entities\DynamicModuleMigration\MigrationTemplateTypes\DynamicModuleMigrationTemplate
     * @throws PhotonException
     */
    public static  function makeByType($type)
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));

        $class = self::$dynamicModuleMigrationTemplatesPath . $className . 'Template';

        if (!class_exists($class)) {
            throw new PhotonException('CANNOT_MAKE_MIGRATION_TEMPLATE_FOR_TYPE', ['type' => $type]);
        }

        return new $class();
    }
}