<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass\Contracts;

use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassTemplateInterface;

interface NativeClassGatewayInterface
{

    /**
     * Saves a model.
     *
     * @param ModelTemplateInterface $template
     * @return boolean
     */
    public function persistFromTemplate($content, NativeClassTemplateInterface $template);

    public function deleteFromTemplate(NativeClassTemplateInterface $template);

    /**
     * Deletes a model.
     *
     * @param string $name
     * @param string $path
     * @return boolean
     */
    public function deleteClassByName($name, $path);
}