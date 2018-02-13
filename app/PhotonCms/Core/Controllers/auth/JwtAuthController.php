<?php

namespace Photon\PhotonCms\Core\Controllers\Auth;

use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Dependencies\DynamicModels\User;
use Validator;

use Photon\PhotonCms\Core\Traits\Jwt\AuthenticatesUsers;
use Photon\PhotonCms\Core\Traits\Jwt\ImpersonatesUsers;
use Photon\PhotonCms\Core\Traits\Jwt\RegistersUsers;
use Photon\PhotonCms\Core\Traits\Jwt\ManagesPasswords;

use Photon\PhotonCms\Core\Entities\UsedPassword\UsedPasswordRepository;
use Photon\PhotonCms\Core\Entities\UsedPassword\UsedPasswordGateway;

use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary;

class JwtAuthController extends Controller
{

    use AuthenticatesUsers, ImpersonatesUsers, RegistersUsers, ManagesPasswords;

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     *
     * @var UsedPasswordRepository
     */
    private $usedPasswordRepository;

    /**
     *
     * @var UsedPasswordGateway
     */
    private $usedPasswordGateway;

    /**
     * @var DynamicModuleLibrary
     */
    private $dynamicModuleLibrary;

    /**
     * Controller construcor.
     *
     * @param ResponseRepository $responseRepository
     * @param UsedPasswordRepository $usedPasswordRepository
     * @param UsedPasswordGateway $usedPasswordGateway
     */
    public function __construct(
        ResponseRepository $responseRepository,
        UsedPasswordRepository $usedPasswordRepository,
        UsedPasswordGateway $usedPasswordGateway,
        DynamicModuleLibrary $dynamicModuleLibrary
    )
    {
        $this->responseRepository     = $responseRepository;
        $this->usedPasswordRepository = $usedPasswordRepository;
        $this->usedPasswordGateway    = $usedPasswordGateway;
        $this->dynamicModuleLibrary   = $dynamicModuleLibrary;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        return User::create($data);
    }

    /**
     * Makes a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator_create(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
        ]);
    }

    /**
     * Makes a validator for an incoming registration request from an invitation.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator_create_with_invitation(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
        ]);
    }

    /**
     * Makes a validator for an incomming change password request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator_change_password($data)
    {
        return Validator::make($data, [
            'old_password' => 'required',
            'new_password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/'
        ]);
    }

    /**
     * Makes a validator for an incomming reset password query request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator_request_reset_password($data)
    {
        return Validator::make($data, [
            'email' => 'required|exists:users,email'
        ]);
    }

    /**
     * Makes a validator for an incomming reset password request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator_reset_password($data)
    {
        return Validator::make($data, [
            'token' => 'required',
            'email' => 'required|exists:users,email',
            'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/'
        ]);
    }
}