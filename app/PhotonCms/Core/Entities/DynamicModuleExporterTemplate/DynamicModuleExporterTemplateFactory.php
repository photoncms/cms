<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExporterTemplate;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class DynamicModuleExporterTemplateFactory
{

    /**
     * Fetches an exporter template file.
     *
     * @param string $path
     * @param string $templateName
     * @return File
     * @throws PhotonException
     */
    public static function make($path, $templateName)
    {

        if (!file_exists("$path/$templateName.blade.php")) {
            throw new PhotonException('DYNAMIC_MODULE_EXPORTER_TEMPLATE_MISSING', ['template_name' => $templateName]);
        }

        return \File::get("$path/$templateName.blade.php");
    }
}
