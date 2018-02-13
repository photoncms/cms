<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Put;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class MassUpdateModuleEntryDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step1 = $steps[0];
        $data = $parameters[0];
        $filter = (key_exists('filter', $parameters[0]))
            ? $parameters[0]['filter']
            : null;

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->massUpdateDynamicModuleEntries($step1->getName(), $data, $filter);
    }
}

