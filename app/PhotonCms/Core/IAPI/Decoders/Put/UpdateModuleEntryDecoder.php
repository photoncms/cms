<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Put;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class UpdateModuleEntryDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step1 = $steps[0];

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->updateDynamicModuleEntry($step1->getName(), $step1->getArgument(0), $parameters[0]);
    }
}

