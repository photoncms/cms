<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass\Contracts;

interface NativeClassCompilerInterface
{
    public function compile(NativeClassTemplateInterface $template);
}