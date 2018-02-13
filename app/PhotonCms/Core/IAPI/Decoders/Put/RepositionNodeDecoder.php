<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Put;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class RepositionNodeDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $action = $parameters[0]['action'];
        $affected = (key_exists('affected', $parameters[0]))
            ? $parameters[0]['affected']
            : null;
        $target = (key_exists('target', $parameters[0]))
            ? $parameters[0]['target']
            : null;

        return app('Photon\PhotonCms\Core\Controllers\NodeController')->repositionDynamicModuleNode($action, $affected, $target);
    }
}