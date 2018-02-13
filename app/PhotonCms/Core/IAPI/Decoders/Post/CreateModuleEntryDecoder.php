<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Post;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class CreateModuleEntryDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step1 = $steps[0];
        $data = $parameters[0];

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->insertDynamicModuleEntry($step1->getName(), $data);
    }
}