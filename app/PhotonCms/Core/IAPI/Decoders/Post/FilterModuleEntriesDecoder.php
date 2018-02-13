<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders\Post;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface;

class FilterModuleEntriesDecoder implements IAPIDecoderInterface
{
    public function decode(array $steps, $parameters = [])
    {
        $step2 = $steps[1];

        if (key_exists(0, $parameters)) {
            $includeRelations = (key_exists('include_relations', $parameters[0]))
                ? $parameters[0]['include_relations']
                : null;
            $filter = (key_exists('filter', $parameters[0]) && is_array($parameters[0]['filter']))
                ? $parameters[0]['filter']
                : null;
            $pagination = (key_exists('pagination', $parameters[0]) && is_array($parameters[0]['pagination']))
                ? $parameters[0]['pagination']
                : null;
            $sorting = (key_exists('sorting', $parameters[0]) && is_array($parameters[0]['sorting']))
                ? $parameters[0]['sorting']
                : null;
        }
        else {
            $includeRelations = null;
            $filter = null;
            $pagination = null;
            $sorting = null;
        }

        return app('Photon\PhotonCms\Core\Controllers\DynamicModuleController')->getAllDynamicModuleEntries($step2->getName(), $includeRelations, $filter, $sorting, $pagination);
    }
}