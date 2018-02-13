<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Get;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class GetNodeAncestorsDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step2 = $steps[1];
        $step3 = $steps[2];

        return app('Photon\PhotonCms\Core\Controllers\NodeController')->getDynamicModuleNodeAncestors($step2->getName(), $step3->getArgument(0));
    }
}