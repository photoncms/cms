<?php

namespace Photon\PhotonCms\Core\Traits\Jwt;

use JWTAuth;
use Illuminate\Http\Request;
use Photon\PhotonCms\Dependencies\DynamicModels\User;
use Photon\PhotonCms\Dependencies\DynamicModels\Invitations;
use Photon\PhotonCms\Core\Helpers\CodeHelper;
use Photon\PhotonCms\Dependencies\AdditionalModuleClasses\Workflows\InvitationWorkflow;
use Carbon\Carbon;
use Photon\PhotonCms\Core\Entities\NotificationHelpers\NotificationHelperFactory;

use Photon\PhotonCms\Dependencies\Notifications\RegistrationConfirmation;
use Photon\PhotonCms\Dependencies\Notifications\RegistrationSuccess;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Illuminate\Support\Facades\Cache;
use \Photon\PhotonCms\Core\Exceptions\PhotonException;

trait RegistersUsers
{

    /**
     * Registers a new user.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->checkLicenseType();

        $validator = $this->validator_create($request->all());

        if ($validator->fails()) {
            return $this->responseRepository->make('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        $registerData = $request->all();

        $registerData['confirmation_code'] = CodeHelper::generateConfirmationCode();

        $user = $this->create($registerData);

        // Update anchor text
        $dynamicModuleLibrary = \App::make('Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary');
        $dynamicModuleLibrary->updateEntryAnchorText($user, 'anchor_text');

        // Add password to used passwords list if necessary
        if (config('jwt.use_password_expiration')) {

            $this->usedPasswordRepository->saveFromData(
                [
                    'user_id' => $user->id,
                    'password' => $user->password
                ],
                $this->usedPasswordGateway
            );

            $user->password_created_at = Carbon::now();
            $user->save();
        }

        // Send an email
        if(\Config::get('photon.use_registration_service_email'))
            $user->notify(new RegistrationConfirmation($user));

        // clear cache
        if(config("photon.use_photon_cache")) {
            $module = ModuleRepository::findByTableNameStatic("users");
            $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
            Cache::tags($relatedModules)->flush(); 
        }

        return $this->responseRepository->make('USER_REGISTER_SUCCESS', ['user' => $user]);
    }

    /**
     * Registers a new user with invitation code.
     * This will automatically set an email address and confirm the user.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function registerWithInvitation($invitationCode, Request $request)
    {
        $this->checkLicenseType();
        
        $validator = $this->validator_create_with_invitation($request->all());

        if ($validator->fails()) {
            return $this->responseRepository->make('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        $invitation = Invitations::whereInvitationCode($invitationCode)->first();

        if (!$invitation) {
            return $this->responseRepository->make('INVALID_USER_INVITATION_CODE');
        }

        $registerData = $request->all();

        $registerData['email'] = $invitation->email;
        $registerData['confirmed'] = true;
        $registerData['roles'] = $invitation->default_role;

        if (InvitationWorkflow::changeInvitationStatusByName($invitation, 'used')) {
            $user = new User();
            $user->setAll($registerData);
            $user->save();

            // Update anchor text
            $dynamicModuleLibrary = \App::make('Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary');
            $dynamicModuleLibrary->updateEntryAnchorText($user, 'anchor_text');

            // Add password to used passwords list if necessary
            if (config('jwt.use_password_expiration')) {

                $this->usedPasswordRepository->saveFromData(
                    [
                        'user_id' => $user->id,
                        'password' => $user->password
                    ],
                    $this->usedPasswordGateway
                );

                $user->password_created_at = Carbon::now();
                $user->save();
            }

            $token = JWTAuth::fromUser($user);
            $user->showRelations();

            // Send an email
            $user->notify(new RegistrationSuccess($user));

            $notificationHelper = NotificationHelperFactory::makeByHelperName("new_user_registered");
            if ($notificationHelper) {
                $notificationHelper->notify($user);
            }

            // clear cache
            if(config("photon.use_photon_cache")) {
                $module = ModuleRepository::findByTableNameStatic("users");
                $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
                Cache::tags($relatedModules)->flush(); 
            }

            return $this->responseRepository->make('USER_REGISTER_SUCCESS', ['user' => $user, 'token' => ['token' => $token, 'ttl' => \Config::get('jwt.ttl')]]);
        }

        return $this->responseRepository->make('INVALID_USER_INVITATION_CODE');
    }

    /**
     * Confirms a newly registered user.
     *
     * @param string $confirmationCode
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Http\Response
     */
    public function confirm($confirmationCode)
    {
        $this->checkLicenseType();
        
        $user = User::whereConfirmationCode($confirmationCode)->first();

        if (!$user) {
            return $this->responseRepository->make('INVALID_USER_CONFIRMATION_CODE');
        }

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->save();

        $token = JWTAuth::fromUser($user);

        // Send an email
        $user->notify(new RegistrationSuccess($user));

        $notificationHelper = NotificationHelperFactory::makeByHelperName("new_user_registered");
        if ($notificationHelper) {
            $notificationHelper->notify($user);
        }

        // clear cache
        if(config("photon.use_photon_cache")) {
            $module = ModuleRepository::findByTableNameStatic("users");
            $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
            Cache::tags($relatedModules)->flush(); 
        }

        return $this->responseRepository->make('USER_CONFIRMATION_SUCCESS', ['token' => ['token' => $token, 'ttl' => \Config::get('jwt.ttl')]]);
    }

    public function checkLicenseType()
    {        
        /*check if more users can be created based on license and domain type*/
        if(Cache::has('photon-license')) {
            $validKey = Cache::get('photon-license');
            $licenseType = $validKey['body']['license_type'];
            $domainType = $validKey['body']['domain_type'];
            $userCount = User::count();

            if(
                // limit number of users if domain type is active
                ($domainType == 2) && 
                (
                    // limit number of users to 1 if license type is developer or developer plus 
                    (in_array($licenseType, [1, 2]) && $userCount >= 1) || 
                    // limit number of users to 2 if license type is basic
                    ($licenseType == 3 && $userCount >= 2)
                )
            ){
                throw new PhotonException('PHOTON_LICENSE_MAX_NUMBER_OF_USERS', ['user_count' => $userCount]);
            }
        }
    }
}