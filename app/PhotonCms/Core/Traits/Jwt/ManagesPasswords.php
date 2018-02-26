<?php

namespace Photon\PhotonCms\Core\Traits\Jwt;

use JWTAuth;
use Illuminate\Http\Request;
use Photon\PhotonCms\Dependencies\DynamicModels\User;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;
use Photon\PhotonCms\Dependencies\Notifications\ResetPassword;

use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

trait ManagesPasswords
{

    /**
     * Changes a user password.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $validator = $this->validator_change_password($request->all());

        if ($validator->fails()) {
            return $this->responseRepository->make('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        $newPassword = $request->get('new_password');

        $credentials['password'] = $request->get('old_password');

        $user = JWTAuth::parseToken()->authenticate();
        $credentials['email'] = $user->email;

        if (JWTAuth::attempt($credentials)) {
            $user->password = bcrypt($newPassword);

            // Add password to used passwords list if necessary
            if (config('jwt.use_password_expiration')) {
                $alreadyUsed = $this->usedPasswordRepository->retrieveByUserId($user->id, $this->usedPasswordGateway);

                foreach ($alreadyUsed as $previousPassword) {
                    if (\Hash::check($newPassword, $previousPassword->password)) {
                        return $this->responseRepository->make('PASSWORD_ALREADY_USED');
                    }
                }

                $this->usedPasswordRepository->saveFromData(
                    [
                        'user_id' => $user->id,
                        'password' => $user->password
                    ],
                    $this->usedPasswordGateway
                );

                $user->password_created_at = Carbon::now();
            }

            $user->save();

            // clear cache
            if(config("photon.use_photon_cache")) {
                $module = ModuleRepository::findByTableNameStatic("users");
                $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
                Cache::tags($relatedModules)->flush(); 
            }

            return $this->responseRepository->make('PASSWORD_CHANGE_SUCCESS');
        }
        else {
            return $this->responseRepository->make('PASSWORD_CHANGE_FAILURE_WRONG_PASSWORD');
        }
    }

    /**
     * Sends an email with a password reset link.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function requestResetPassword(Request $request)
    {
        $validator = $this->validator_request_reset_password($request->all());

        if ($validator->fails()) {
            return $this->responseRepository->make('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        $email = $request->get('email');

        $user = User::whereEmail($email)->first();

        $user->notify(new ResetPassword($user));

        return $this->responseRepository->make('PASSWORD_RESET_REQUEST_SUCCESS');
    }

    /**
     * Resets a password.
     *
     * Actually this method uses a token which was provided to the user and also
     * uses the submitted email and password to set the new password.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $validator = $this->validator_reset_password($request->all());

        if ($validator->fails()) {
            return $this->responseRepository->make('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        $email = $request->get('email');
        $token = $request->get('token');

        $user = User::whereEmail($email)->first();

        if (!$user) {
            return $this->responseRepository->make('PASSWORD_RESET_INVALID_USER');
        }

        if (\Password::getRepository()->exists($user, $token)) {
            $newPassword = $request->get('password');
            $user->password = bcrypt($newPassword);

            // Add password to used passwords list if necessary
            if (config('jwt.use_password_expiration')) {
                $alreadyUsed = $this->usedPasswordRepository->retrieveByUserId($user->id, $this->usedPasswordGateway);

                foreach ($alreadyUsed as $previousPassword) {
                    if (\Hash::check($newPassword, $previousPassword->password)) {
                        return $this->responseRepository->make('PASSWORD_ALREADY_USED');
                    }
                }

                $this->usedPasswordRepository->saveFromData(
                    [
                        'user_id' => $user->id,
                        'password' => $user->password
                    ],
                    $this->usedPasswordGateway
                );

                $user->password_created_at = Carbon::now();
            }

            $user->save();
            \Password::getRepository()->delete($user);

            return $this->responseRepository->make('PASSWORD_RESET_SUCCESS');
        }
        else {
            return $this->responseRepository->make('PASSWORD_RESET_INVALID_TOKEN');
        }
    }
}