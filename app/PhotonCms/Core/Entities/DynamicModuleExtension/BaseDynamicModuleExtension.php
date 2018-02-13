<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExtension;

use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

// Enable/disable usage of these interfaces according to your needs.
// Functions promissed by an interface will never be called if the interface which promisses them isn't implemented for the class.
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionCanInterruptCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionCanInterruptRetrieve;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionCanInterruptUpdate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionCanInterruptDelete;

use Photon\PhotonCms\Core\PermissionServices\PermissionChecker;

/**
 * This is an example of the dynamic module extension class. This is the second layer controller of the HMVC architecture.
 * Walk through comments in this file to get familiar with each section.
 */
class BaseDynamicModuleExtension implements
    ModuleExtensionCanInterruptCreate,
    ModuleExtensionCanInterruptRetrieve,
    ModuleExtensionCanInterruptUpdate,
    ModuleExtensionCanInterruptDelete
{
    /**
     * ResponseRepository instance
     *
     * @var ResponseRepository
     */
    protected $responseRepository;

    /**
     * Module table name.
     *
     * @var string
     */
    protected $tableName;

    /**
     * An array of data which is used to manipulate the model.
     * This array is decoupling from the Request facade, and is passed as reference throughout
     * the process in order to preserve input changes which were made in hmvc methods.
     *
     * @var array
     */
    protected $requestData;

    /**
     * This constructor represents a part of an HMVC architecture.
     * The class is (and should be) instantiated allways through laravel's IoC container, thus
     * providing a possibility to typehint dependency injections into the class constructor.
     *
     * @param ResponseRepository $responseRepository
     */
    public function __construct(ResponseRepository $responseRepository)
    {
        $this->responseRepository = $responseRepository;
    }

    /**
     * Sets the data array for decoupling from the Request facade.
     *
     * @param array $data
     */
    public function setRequestData(&$data)
    {
        $this->requestData =& $data;
    }

    /**
     * Sets the module table name.
     *
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /*****************************************************************
     * These functions represent interrupters for regular dynamic module entry flow.
     * If an instance of \Illuminate\Http\Response is returned, the rest of the flow after it will be interrupted.
     */
    public function interruptCreate()
    {
        // Access
        if (!PermissionChecker::canModifyModule($this->tableName)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_access' => $this->tableName]);
        }

        // Permission
        if (!PermissionChecker::canCRUDCreate($this->tableName)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_create' => $this->tableName]);
        }

        // Restrictions
        if (!PermissionChecker::canCRUDCreateMatchingRequestData($this->tableName, $this->requestData)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_create' => $this->tableName]);
        }
    }

    public function interruptRetrieve(&$entries)
    {
    }

    public function interruptUpdate($entry)
    {
        // Access
        if (!PermissionChecker::canModifyModule($this->tableName)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_access' => $this->tableName]);
        }

        // Permission
        if (!PermissionChecker::canCRUDUpdate($this->tableName)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_update' => $this->tableName]);
        }

        // Restrictions
        if (!PermissionChecker::canCRUDUpdateMatchingModuleEntry($this->tableName, $entry)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_update' => $entry->id]);
        }
    }

    public function interruptDelete($entry)
    {
        // Access
        if (!PermissionChecker::canModifyModule($this->tableName)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_access' => $this->tableName]);
        }

        // Permission
        if (!PermissionChecker::canCRUDDelete($this->tableName)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_update' => $this->tableName]);
        }

        // Restrictions
        if (!PermissionChecker::canCRUDDeleteMatchingModuleEntry($this->tableName, $entry)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_update' => $entry->id]);
        }
    }

    protected function getRequestParameter($parameterName)
    {
        return (array_key_exists($parameterName, $this->requestData))
            ? $this->requestData[$parameterName]
            : null;
    }
}