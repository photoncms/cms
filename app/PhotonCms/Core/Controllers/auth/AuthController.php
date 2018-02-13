<?php

namespace Photon\PhotonCms\Core\Controllers\Auth;

use Photon\PhotonCms\Core\Entities\User\User;
use Validator;
use Photon\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Photon\PhotonCms\Core\Traits\Auth\AuthenticatesAndRegistersUsers;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @param ResponseRepository $responseRepository
     * @return void
     */
    public function __construct(
        ResponseRepository $responseRepository
    )
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->responseRepository = $responseRepository;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
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
     * Actually gets the user by the submitted bearer token in the header.
     *
     * @param Request $request
     * @return \Illuminate\Http\Request
     */
    public function getLoggedInUser(Request $request)
    {
        $user = User::whereApiToken(
            str_replace('Bearer ', '', $request->header('Authorization'))
        )->first();

        return $this->responseRepository->make('GET_LOGGED_IN_USER_SUCCESS', ['user' => $user]);
    }
}
