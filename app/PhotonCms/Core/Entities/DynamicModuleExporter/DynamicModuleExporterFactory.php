<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExporter;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class DynamicModuleExporterFactory
{
    private static $supportedTypes = [
        'XLSX',
        'PDF',
        'CSV',
    ];

    public static function make($className, $type)
    {
        if (!in_array($type, self::$supportedTypes)) {
            throw new PhotonException('EXPORTER_TYPE_NOT_SUPPORTED', ['type' => $type]);
        }

        $class = config('photon.dynamic_module_exporters_namespace') . "$className\\" . $className .ucfirst($type);

        if (!class_exists($class)) {
            throw new PhotonException('DYNAMIC_MODULE_EXPORTER_MISSING', ['class' => $class]);
        }

        return new $class(strtolower($type));
    }
}