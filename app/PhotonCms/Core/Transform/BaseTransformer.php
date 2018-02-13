<?php

namespace Photon\PhotonCms\Core\Transform;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

/**
 * Transforms object instances into various output packages.
 */
abstract class BaseTransformer extends TransformerAbstract
{

    /**
     * Transforms all generic objects.
     *
     * @param array $array
     */
    protected function transformGenericObjects(array &$array)
    {
        foreach ($array as &$item) {
            if (is_object($item)) {
                if ($item instanceof Carbon) {
                    $item = $item->toIso8601String();
                }
            }
        }
    }
}