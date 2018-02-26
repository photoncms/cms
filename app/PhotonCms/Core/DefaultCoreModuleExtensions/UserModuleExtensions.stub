<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Illuminate\Http\Response;
use \Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;
use Illuminate\Support\Facades\Cache;

use Photon\PhotonCms\Dependencies\DynamicModels\User;
use Photon\PhotonCms\Dependencies\DynamicModels\Roles;
use Photon\PhotonCms\Core\PermissionServices\PermissionChecker;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;
use Photon\PhotonCms\Core\Helpers\CodeHelper;
use Photon\PhotonCms\Core\Entities\UsedPassword\UsedPasswordRepository;
use Photon\PhotonCms\Core\Entities\UsedPassword\UsedPasswordGateway;
use Carbon\Carbon;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Dependencies\Notifications\RegistrationConfirmation;
use Photon\PhotonCms\Core\Entities\EmailChangeRequest\EmailChangeRequest;
use Photon\PhotonCms\Dependencies\Notifications\EmailChangeConfirmation;
use Photon\PhotonCms\Dependencies\Notifications\EmailChangeSuccess;

use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreUpdate;
use Photon\PhotonCms\Core\Traits\Jwt\RegistersUsers;

/**
 * These are functionality extensions for the Invitations module.
 */
class UserModuleExtensions extends BaseDynamicModuleExtension implements
    ModuleExtensionHandlesPostCreate,
    ModuleExtensionHandlesPreUpdate
{

    public function __construct(
        UsedPasswordRepository $usedPasswordRepository,
        UsedPasswordGateway $usedPasswordGateway,
        ResponseRepository $responseRepository
    )
    {
        $this->usedPasswordRepository = $usedPasswordRepository;
        $this->usedPasswordGateway    = $usedPasswordGateway;
        parent::__construct($responseRepository);
    }

    use RegistersUsers;

    /*****************************************************************
     * These functions represent interrupters for regular dynamic module entry flow.
     * If an instance of \Illuminate\Http\Response is returned, the rest of the flow after it will be interrupted.
     */
    public function interruptCreate()
    {
        $this->checkLicenseType();

        $interrupt = parent::interruptCreate($this->requestData);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }

        if (array_key_exists('roles', $this->requestData)) {
            $newRoles = $this->requestData['roles'];

            if ($newRoles === '' || !$newRoles) {
                $newRoles = [];
            }
            if (!is_array($newRoles)) {
                $newRoles = explode(',', $newRoles);
            }

            $newRoles = (empty($newRoles)) ? [] : Roles::findMany($newRoles)->pluck('id')->toArray();

            $cannotAssign = [];
            foreach ($newRoles as $roleId) {
                $role = Roles::find($roleId);
                if (!PermissionChecker::canAssignRole($role->name)) {
                    $cannotAssign[] = $role->title;
                }
            }

            if (!empty($cannotAssign)) {
                throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_assign' => $cannotAssign]);
            }
        }
    }

    public function interruptUpdate($user)
    {
        $interrupt = parent::interruptUpdate($user);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }

        if (array_key_exists('roles', $this->requestData)) {
            $newRoles = $this->requestData['roles'];

            if ($newRoles === '' || !$newRoles) {
                $newRoles = [];
            }
            if (!is_array($newRoles)) {
                $newRoles = explode(',', $newRoles);
            }

            $rolesAssignmentRules = config('photon.role_assignment_rules');

            if ($user) {
                $newRoles = (empty($newRoles)) ? [] : Roles::findMany($newRoles)->pluck('id')->toArray();
                $currentRoles = $user->roles_relation->pluck('id')->toArray();
                $rolesForRevoking = array_diff($currentRoles, $newRoles);
                $rolesForAssigning = array_diff($newRoles, $currentRoles);

                foreach ($rolesAssignmentRules as $roleAssignmentRules) {
                    $role = Roles::whereName($roleAssignmentRules['role'])->first();
                    $roleAssignmentCount = User::whereHas('roles_relation', function ($q) use ($roleAssignmentRules, $user) {
                        $q->where('name', $roleAssignmentRules['role']);
                        if (isset($roleAssignmentRules['match']) && !empty($roleAssignmentRules['match'])) {
                            foreach ($roleAssignmentRules['match'] as $matchField) {
                                $q->where($matchField, $user->$matchField);
                            }
                        }
                    })->count();

                    if (in_array($role->id, $rolesForRevoking) && $roleAssignmentCount <= $roleAssignmentRules['min']) {
                        throw new PhotonException('MIN_USERS_PER_ROLE_REACHED');
                    }
                }

                $cannotRevoke = [];
                foreach ($rolesForRevoking as $roleId) {
                    $role = Roles::find($roleId);
                    if (!PermissionChecker::canRevokeRole($role->name)) {
                        $cannotRevoke[] = $role->title;
                    }
                }

                $cannotAssign = [];
                foreach ($rolesForAssigning as $roleId) {
                    $role = Roles::find($roleId);
                    if (!PermissionChecker::canAssignRole($role->name)) {
                        $cannotAssign[] = $role->title;
                    }
                }

                if (!empty($cannotAssign) || !empty($cannotRevoke)) {
                    throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_assign' => $cannotAssign, 'cannot_revoke' => $cannotRevoke]);
                }
            }
        }

        // Add password to used passwords list if necessary
        if (config('jwt.use_password_expiration') && array_key_exists('password', $this->requestData)) {
            $newPassword = $this->requestData['password'];
            $alreadyUsed = $this->usedPasswordRepository->retrieveByUserId($user->id, $this->usedPasswordGateway);

            foreach ($alreadyUsed as $previousPassword) {
                if (\Hash::check($newPassword, $previousPassword->password)) {
                    throw new PhotonException('PASSWORD_ALREADY_USED');
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
    }

    public function interruptDelete($entry)
    {
        $interrupt = parent::interruptDelete($entry);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }

        $currentUser = \Auth::user();

        if ($currentUser->id == $entry->id) { // Entry id can happen to be a string accidentally
            throw new PhotonException('CANNOT_DELETE_SELF');
        }
    }

    /*****************************************************************
     * These functions represent the event handlers for create/update/delete actions over a dynamic module model.
     * Their return is not handled, so returning responses from here is useless.
     * Each function can be interrupted by throwing an exception. Throwing an exception will also stop the whole process. For
     * example, if a preCreate function throws an exception, this means, since the object hasn't been saved yet, the object
     * will never be saved at all.
     */
    public function postCreate($item, $cloneAfter)
    {
        // Add password to used passwords list if necessary
        if (config('jwt.use_password_expiration')) {
            $confirmatonCode = CodeHelper::generateConfirmationCode();

            $item->confirmation_code = $confirmatonCode;
            $item->confirmed = false;

            $this->usedPasswordRepository->saveFromData(
                [
                    'user_id' => $item->id,
                    'password' => $item->password
                ],
                $this->usedPasswordGateway
            );

            $item->password_created_at = Carbon::now();
            $item->save();

            if(\Config::get('photon.use_registration_service_email'))
                $item->notify(new RegistrationConfirmation($item));
        }
    }

    public function preUpdate($item, $cloneBefore, $cloneAfter)
    {
        if(\Auth::user()->id != $item->id)
            return true;

        if (isset($cloneAfter->email) && $cloneBefore->email !== $cloneAfter->email) {
            $newEmail = $cloneAfter->email;

            // Revert the email, it mustn't be changed at this point
            $item->email = $cloneBefore->email;

            // Prepare the confirmation code for email change
            $confirmatonCode = CodeHelper::generateConfirmationCode();

            // Store the email change request
            $emailChangeRequestData = [
                'user_id' => $item->id,
                'email' => $newEmail,
                'confirmation_code' => $confirmatonCode,
            ];

            $changeRequest = new EmailChangeRequest($emailChangeRequestData);
            $changeRequest->save();

            $changeRequest->notify(new EmailChangeConfirmation($newEmail, $confirmatonCode));
        }
    }

    /*****************************************************************
     * This function if required by the interface, is used for outputting pre-compiled extension function calls through the API.
     * The result should be in form of an associative array with keys representing the human-readable name of the function call
     * and the value should be the API path for the call.
     *
     * The action_name in the path will be capitalized and added a prefix 'call'. For example, 'myAction' action name in the
     * path will result in calling of 'callMyAction'.
     * This is to prevent any other public methods from being called through the api illegally.
     */
    public function getExtensionFunctionCalls($item)
    {
        return [
            'Confirm email change' => "/api/extension_call/{$item->getTable()}/{$item->id}/confirmEmailChange",
        ];
    }

    /*****************************************************************
     * Following functions represent all of the extended custom functionality. Each of these functions should be 'registered'
     * within the $this->getExtensionFunctionCalls() return array. Each function name will be used without the 'call'prefix and
     * ucfirst.
     */
    public function callConfirmEmailChange($item, $code)
    {
        $emailChangeRequest = EmailChangeRequest::whereConfirmationCode($code)->whereUserId($item->id)->whereUsed(false)->first();

        if ($emailChangeRequest) {
            $item->email = $emailChangeRequest->email;
            $item->save();
            $emailChangeRequest->used = true;
            $emailChangeRequest->save();
            $item->notify(new EmailChangeSuccess());

            // user log out so it can log in with a new email now
            $oldToken = \JWTAuth::getToken();
            \JWTAuth::invalidate($oldToken);
        }
        else {
            throw new PhotonException('INVALID_EMAIL_CHANGE_CONFIRMATION_CODE');
        }

        return $this->responseRepository->make('EMAIL_ADDRESS_CHANGED');
    }
}
