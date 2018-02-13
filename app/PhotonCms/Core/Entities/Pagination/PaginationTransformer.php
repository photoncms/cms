<?php

namespace Photon\PhotonCms\Core\Entities\Pagination;

use Photon\PhotonCms\Core\Transform\BaseTransformer;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Transforms LengthAwarePaginator instances into various output packages.
 */
class PaginationTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var LengthAwarePaginator $object
     * @return array
     */
    public function transform(LengthAwarePaginator $object)
    {
        $data = [
            'total'         => (int) $object->total(),
            'count'         => (int) $object->count(),
            'current_page'  => (int) $object->currentPage(),
            'has_more_pages'=> (bool) $object->hasMorePages(),
            'last_page'     => (int) $object->lastPage(),
            'per_page'      => (int) $object->perPage()
        ];
        
        return $data;
    }
}