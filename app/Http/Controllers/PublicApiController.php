<?php

namespace Photon\Http\Controllers;

use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Response\ResponseRepository;

class PublicApiController extends Controller
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * Controller construcor.
     *
     * @param ResponseRepository $responseRepository
     */
    public function __construct(
        ResponseRepository $responseRepository
    )
    {
        $this->responseRepository = $responseRepository;
    }
}