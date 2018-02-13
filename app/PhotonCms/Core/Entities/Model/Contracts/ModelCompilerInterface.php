<?php

namespace Photon\PhotonCms\Core\Entities\Model\Contracts;

use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface;

interface ModelCompilerInterface
{
    public function compile(ModelTemplateInterface $template);
}