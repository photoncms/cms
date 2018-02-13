<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Post;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class CountModuleEntriesDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step2 = $steps[1];

        $filter = (key_exists(0, $parameters) && key_exists('filter', $parameters[0]) && is_array($parameters[0]['filter']))
            ? $parameters[0]['filter']
            : null;

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->countDynamicModuleEntries($step2->getName(), $filter);
    }
}