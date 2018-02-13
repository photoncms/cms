<?php

namespace Photon\PhotonCms\Core\Traits\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Photon\PhotonCms\Core\Helpers\CodeHelper;

use Illuminate\Foundation\Auth\RedirectsUsers;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return $this->showRegistrationForm();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        if (property_exists($this, 'registerView')) {
            return view($this->registerView);
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        return $this->register($request);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $useConfirmationEmail = \Config::get('photon.use_registration_service_email');

        if ($useConfirmationEmail) {
            return $this->registerWithConfirmation($request);
        }
        else {
            return $this->registerWithoutConfirmation($request);
        }
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerWithConfirmation(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->responseRepository->make('VALIDATION_ERROR', ['error_fields' => $validator]);
            } else {
                $this->throwValidationException(
                    $request, $validator
                );
            }
        }

        $registerData = $request->all();

        $registerData['confirmation_code'] = CodeHelper::generateConfirmationCode();

        $user = $this->create($registerData);

        \Mail::send('auth.emails.welcome', ['confirmation_code' => $registerData['confirmation_code'], 'email' => $registerData['email']], function($message) use ($registerData) {
            $message->to($registerData['email'])
                    ->from(\Config::get('photon.registration_service_email'))
                    ->subject(\Config::get('photon.registration_email_title'));
        });

        if ($request->ajax() || $request->wantsJson()) {
            return $this->responseRepository->make('USER_REGISTER_SUCCESS', ['user' => $user]);
        } else {
            Auth::guard($this->getGuard())->login($user);

            return redirect($this->redirectPath());
        }
    }

    /**
     * Registers a user without confirmation
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function registerWithoutConfirmation(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->responseRepository->make('VALIDATION_ERROR', ['error_fields' => $validator]);
            } else {
                $this->throwValidationException(
                    $request, $validator
                );
            }
        }

        $registerData = $request->all();
        $registerData['confirmed'] = 1;

        $user = $this->create($registerData);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->responseRepository->make('USER_REGISTER_SUCCESS', ['user' => $user]);
        } else {
            Auth::guard($this->getGuard())->login($user);

            return redirect($this->redirectPath());
        }
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return string|null
     */
    protected function getGuard()
    {
        return property_exists($this, 'guard') ? $this->guard : null;
    }
}
