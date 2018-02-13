<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Get;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class GetRootNodesDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step2 = $steps[1];

        if (!is_array($parameters) || empty($parameters)) {
            $parameters = null;
        }

        return app('Photon\PhotonCms\Core\Controllers\NodeController')->getDynamicModuleNodeChildren($step2->getName(), null, $parameters);
    }
}