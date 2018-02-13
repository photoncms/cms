<?php

namespace Photon\PhotonCms\Core\Controllers\Auth;

use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Traits\Auth\ResetsPasswords;
use Photon\PhotonCms\Core\Response\ResponseRepository;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct(
        ResponseRepository $responseRepository
    )
    {
        $this->middleware('guest');
        $this->responseRepository = $responseRepository;
    }
}
