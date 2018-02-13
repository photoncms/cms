<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Get;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class GetModuleItemDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step1 = $steps[0];

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->getDynamicModuleEntry($step1->getName(), $step1->getArgument(0));
    }
}