<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass;

use Config;

use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassTemplateInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassGatewayInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassCompilerInterface;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over Model entity.
 */
class NativeClassRepository
{
    /**
     * Saves class content to a file from template.
     * 
     * @param NativeClassTemplateInterface $classTemplate
     * @param NativeClassGatewayInterface $classGateway
     */
    public function saveFromTemplate(NativeClassTemplateInterface $template, NativeClassCompilerInterface $compiler, NativeClassGatewayInterface $gateway)
    {
        $content = $compiler->compile($template);

        return $gateway->persistFromTemplate($content, $template);
    }

    /**
     * Deletes a class file using a NativeClassTemplate instance
     *
     * @param NativeClassTemplateInterface $classTemplate
     * @param NativeClassGatewayInterface $classGateway
     * @return boolean
     */
    public function deleteFromTemplate (NativeClassTemplateInterface $template, NativeClassGatewayInterface $gateway)
    {
        return $gateway->deleteFromTemplate($template);
    }

    /**
     * Deletes a class file by its class name.
     * This assumes that the class file was created under identical class name.
     *
     * @param string $name
     * @param string $path
     * @param NativeClassGatewayInterface $classGateway
     * @return boolean
     */
    public function deleteClassByName($name, $path = null, NativeClassGatewayInterface $gateway)
    {
        return $gateway->deleteClassByName($name, $path);
    }
}