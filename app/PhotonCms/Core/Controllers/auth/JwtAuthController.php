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
use Photon\PhotonCms\Core\Entities\Field\FieldRepository;
use Photon\PhotonCms\Core\Entities\Field\Contracts\FieldGatewayInterface;

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
     * @var FieldRepository
     */
    private $fieldRepository;

    /**
     * @var FieldGatewayInterface
     */
    private $fieldGateway;

    /**
     * Controller construcor.
     *
     * @param ResponseRepository $responseRepository
     * @param UsedPasswordRepository $usedPasswordRepository
     * @param UsedPasswordGateway $usedPasswordGateway
     * @param FieldRepository $fieldRepository
     * @param FieldGatewayInterface $fieldGateway
     */
    public function __construct(
        ResponseRepository $responseRepository,
        UsedPasswordRepository $usedPasswordRepository,
        UsedPasswordGateway $usedPasswordGateway,
        DynamicModuleLibrary $dynamicModuleLibrary,
        FieldRepository $fieldRepository,
        FieldGatewayInterface $fieldGateway
    )
    {
        $this->responseRepository     = $responseRepository;
        $this->usedPasswordRepository = $usedPasswordRepository;
        $this->usedPasswordGateway    = $usedPasswordGateway;
        $this->dynamicModuleLibrary   = $dynamicModuleLibrary;
        $this->fieldRepository        = $fieldRepository;
        $this->fieldGateway           = $fieldGateway;
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
        $columnsForValidation = config("photon.photon_register_use_columns");

        $fields = $this->fieldRepository->findByModuleId(1, $this->fieldGateway);

        // Regular user-defined validation
        $validationRules = [];
        foreach ($fields as $field) {
            $uniqueName = $field->getUniqueName();
            if ($field->validation_rules && in_array($uniqueName, $columnsForValidation)) {
                $validationRules[$uniqueName] = $field->validation_rules;
            }
        }

        return Validator::make($data, $validationRules);
    }

    /**
     * Makes a validator for an incoming registration request from an invitation.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator_create_with_invitation(array $data)
    {
        $columnsForValidation = config("photon.photon_register_use_columns");

        if (($key = array_search('email', $columnsForValidation)) !== false) {
            unset($columnsForValidation[$key]);
        }

        $fields = $this->fieldRepository->findByModuleId(1, $this->fieldGateway);

        // Regular user-defined validation
        $validationRules = [];
        foreach ($fields as $field) {
            $uniqueName = $field->getUniqueName();
            if ($field->validation_rules && in_array($uniqueName, $columnsForValidation)) {
                $validationRules[$uniqueName] = $field->validation_rules;
            }
        }

        return Validator::make($data, $validationRules);
    }

    /**
     * Makes a validator for an incomming change password request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator_change_password($data)
    {
        $fields = $this->fieldRepository->findByModuleId(1, $this->fieldGateway);

        $field = $fields->firstWhere('column_name', 'password');

        return Validator::make($data, [
            'old_password' => 'required',
            'new_password' => $field->validation_rules,
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
        $fields = $this->fieldRepository->findByModuleId(1, $this->fieldGateway);

        $field = $fields->firstWhere('column_name', 'password');

        return Validator::make($data, [
            'token' => 'required',
            'email' => 'required|exists:users,email',
            'password' => $field->validation_rules,
        ]);
    }
}
