<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Illuminate\Http\Response;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;

// Enable/disable usage of these interfaces according to your needs.
// Functions promissed by an interface will never be called if the interface which promisses them isn't implemented for the class.
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasExtensionFunctions;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreUpdate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostUpdate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreDelete;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostDelete;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasGetterExtension;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasSetterExtension;

/**
 * This is an example of the dynamic module extension class. This is the second layer controller of the HMVC architecture.
 * Walk through comments in this file to get familiar with each section.
 */
class DynamicModuleExtensionsExample extends BaseDynamicModuleExtension implements
    ModuleExtensionHasExtensionFunctions,
    ModuleExtensionHandlesPreCreate,
    ModuleExtensionHandlesPostCreate,
    ModuleExtensionHandlesPreUpdate,
    ModuleExtensionHandlesPostUpdate,
    ModuleExtensionHandlesPreDelete,
    ModuleExtensionHandlesPostDelete,
    ModuleExtensionHasGetterExtension,
    ModuleExtensionHasSetterExtension
{

    /*****************************************************************
     * These functions represent interrupters for regular dynamic module entry flow.
     * If an instance of \Illuminate\Http\Response is returned, the rest of the flow after it will be interrupted.
     */
    public function interruptCreate()
    {
        $interrupt = parent::interruptCreate();
        if ($interrupt instanceof Response) {
            return $interrupt;
        }
    }

    public function interruptRetrieve(&$entries)
    {
        $interrupt = parent::interruptRetrieve($entries);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }
    }

    public function interruptUpdate($entry)
    {
        $interrupt = parent::interruptUpdate($entry);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }
    }

    public function interruptDelete($entry)
    {
        $interrupt = parent::interruptDelete($entry);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }
    }

    /*****************************************************************
     * These functions represent the event handlers for create/update/delete actions over a dynamic module model.
     * Their return is not handled, so returning responses from here is useless.
     * Each function can be interrupted by throwing an exception. Throwing an exception will also stop the whole process. For
     * example, if a preCreate function throws an exception, this means, since the object hasn't been saved yet, the object
     * will never be saved at all.
     */
    public function preCreate($item, $cloneAfter)
    {
    }

    public function postCreate($item, $cloneAfter)
    {
    }

    public function preRetrieve()
    {
    }

    public function postRetrieve($item)
    {
    }

    public function preUpdate($item, $cloneBefore, $cloneAfter)
    {
    }

    public function postUpdate($item, $cloneBefore, $cloneAfter)
    {
    }

    public function postDelete($item)
    {
    }

    public function preDelete($item, $cloneBefore)
    {
    }

    /*****************************************************************
     * Following funcitons extend models getAll() and setAll() methods.
     * Getter extension should return an array which will be appended to the array compiled by the model.
     * Setter extension should set any additional attributes for the model which are not automatically set
     * by the setAll() method.
     */
    public function executeGetterExtension($entry)
    {

    }

    public function executeSetterExtension($entry)
    {

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
            //'My Function' => '/api/extension_call/{table_name}/{item_id}/{action_name}',
            'Test' => '/api/extension_call/test_table/1/test'
        ];
    }

    /*****************************************************************
     * Following functions represent all of the extended custom functionality. Each of these functions should be 'registered'
     * within the $this->getExtensionFunctionCalls() return array. Each function name will be used without the 'call'prefix and
     * ucfirst.
     */
    public function callTest($item)
    {
        // This kind of response will return a regular response from photon core response configuration file.
        //return $this->responseRepository->make('TEST_RESPONSE', ['name' => $item->name]);

        // This kind of response will load response codes from a configuration file named 'responsescustom'. Any file can be
        // created and used.
        //return $this->responseRepository->make('TEST_RESPONSE', ['name' => $item->name], 'responsescustom');
    }
}
