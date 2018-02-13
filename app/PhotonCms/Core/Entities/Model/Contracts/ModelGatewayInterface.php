<?php

namespace Photon\PhotonCms\Core\Entities\Model\Contracts;

use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface;

interface ModelGatewayInterface
{

    /**
     * Saves a model.
     *
     * @param ModelTemplateInterface $template
     * @return boolean
     */
    public function persistFromTemplate($content, ModelTemplateInterface $template);

    public function deleteFromTemplate(ModelTemplateInterface $template);

    /**
     * Deletes a model.
     *
     * @param string $name
     * @param string $path
     * @return boolean
     */
    public function deleteClassByName($name);
}