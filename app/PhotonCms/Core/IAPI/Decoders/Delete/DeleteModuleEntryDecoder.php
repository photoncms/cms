<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Delete;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class DeleteModuleEntryDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step1 = $steps[0];
        $force = (is_array($parameters) && key_exists(0, $parameters) && key_exists('force', $parameters[0]))
            ? $parameters[0]['force']
            : false;

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->deleteDynamicModuleEntry($step1->getName(), $step1->getArgument(0), $force);
    }
}