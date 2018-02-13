<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Get;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class GetModuleEntriesDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step1 = $steps[0];

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->getAllDynamicModuleEntries($step1->getName());
    }
}