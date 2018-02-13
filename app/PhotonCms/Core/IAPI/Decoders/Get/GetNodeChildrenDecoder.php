<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Get;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class GetNodeChildrenDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step2 = $steps[1];

        $childModules = (is_array($parameters) && key_exists(0, $parameters) && key_exists('child_modules', $parameters[0]))
            ? $parameters[0]['child_modules']
            : null;

        return app('Photon\PhotonCms\Core\Controllers\NodeController')->getDynamicModuleNodeChildren($step2->getName(), $step2->getArgument(0), $childModules);
    }
}