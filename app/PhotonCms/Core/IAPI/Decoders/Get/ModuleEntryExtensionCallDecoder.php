<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Get;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class ModuleEntryExtensionCallDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step2 = $steps[1];
        $step3 = $steps[2];
        $parameters = implode('\\', $parameters);

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->callExtension($step2->getName(), $step2->getArgument(0), $step3->getName(), $parameters);
    }
}