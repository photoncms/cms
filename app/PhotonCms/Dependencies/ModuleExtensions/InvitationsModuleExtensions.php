<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Photon\PhotonCms\Dependencies\DynamicModels\Invitations;
use Photon\PhotonCms\Dependencies\DynamicModels\Assets;
use Photon\PhotonCms\Core\Helpers\CodeHelper;

use League\Csv\Reader;
use \Photon\PhotonCms\Core\Exceptions\PhotonException;
use Illuminate\Http\Response;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;

use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasExtensionFunctions;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostCreate;

use Photon\PhotonCms\Dependencies\AdditionalModuleClasses\Workflows\InvitationWorkflow;

/**
 * These are functionality extensions for the Invitations module.
 */
class InvitationsModuleExtensions extends BaseDynamicModuleExtension implements
    ModuleExtensionHasExtensionFunctions,
    ModuleExtensionHandlesPreCreate,
    ModuleExtensionHandlesPostCreate
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

        $csvFile = $this->getRequestParameter('csv_file');
        $email = $this->getRequestParameter('email');

        $asset = Assets::find($csvFile);

        if ($csvFile) {
            if (!$asset || !\Storage::disk('assets')->exists($asset->storage_file_name)) {
                throw new PhotonException('CSV_FILE_DOESNT_EXIST', ['file' => $csvFile]);
            }

            $filepath = config('filesystems.disks.assets.root').'/'.$asset->storage_file_name;

            $inputCsv = Reader::createFromPath($filepath);
            $inputCsv->setDelimiter(';');

            $headers = $inputCsv->fetchOne(0);
            $hasHeaders = in_array('email', $headers);

            $rows = $inputCsv->setOffset($hasHeaders ? 1 : 0)->fetch();

            $invitations = [];

            $allData = [];
            $counter = 1;
            foreach ($rows as $row) {
                $singleData = [
                    'email' => $row[0],
                ];

                $validator = \Validator::make($singleData, [
                    'email' => 'email|unique:users',
                ]);

                if (!$validator->passes()) {
                    throw new PhotonException('INVALID_CSV_FILE', ['error_row' => $counter, 'error_data' => $singleData]);
                }

                $allData[] = $singleData;

                $counter++;
            }

            // Destroy any open file streams.
            unset($inputCsv);
            unset($rows);

            foreach ($allData as $singleData) {
                // Create the invitation
                $newInvitation = new Invitations();
                $input = [
                    'email' => $singleData['email'],
                ];
                $newInvitation->setAll($input);

                // Generate invitation code
                $newInvitation->invitation_code = CodeHelper::generateInvitationCode();
                $newInvitation->save();

                // Update status
                InvitationWorkflow::changeInvitationStatusByName($newInvitation, 'pending', true);
                $newInvitation->load('invitation_status_relation');

                // Update anchor text
                $dynamicModuleLibrary = \App::make('Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary');
                $dynamicModuleLibrary->updateEntryAnchorText($newInvitation, 'anchor_text');

                if (config('photon.use_slugs')) {
                    // Update anchor text
                    $dynamicModuleLibrary->updateEntryAnchorText($newInvitation, 'slug');
                }

                // Prepare relations and output
                $newInvitation->showRelations();
                $invitations[] = $newInvitation;
            }

            // Delete the file after it has been used
            \Storage::disk('assets')->delete($asset->storage_file_name);
            $asset->delete();
            
            throw new PhotonException('SAVE_DYNAMIC_MODULE_ENTRIES_SUCCESS', ['invitations' => $invitations]);
        }
        else {
            $validator = \Validator::make(['email' => $email], [
                'email' => 'required|unique:users,email|max:255'
            ]);
        }
    }

    /*****************************************************************
     * These functions represent the event handlers for create/update/delete actions over a dynamic module model.
     * Their return is not handled, so returning responses from here is useless.
     * Each function can be interrupted by throwing an exception. Throwing an exception will also stop the whole process. For
     * example, if a preCreate function throws an exception, this means, since the object hasn't been saved yet, hte bject
     * will never be saved at all.
     */
    public function preCreate($item, $cloneAfter)
    {
        $item->invitation_code = CodeHelper::generateInvitationCode();
    }

    public function postCreate($item, $cloneAfter)
    {
        InvitationWorkflow::changeInvitationStatusByName($item, 'pending', true);
        // Load the status additionally because it gets lost in the process of changing.
        $item->load('invitation_status_relation');
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
            'Resend' => "/api/extension_call/{$item->getTable()}/{$item->id}/resend",
            'Cancel' => "/api/extension_call/{$item->getTable()}/{$item->id}/cancel"
        ];
    }

    /*****************************************************************
     * Following functions represent all of the extended custom functionality. Each of these functions should be 'registered'
     * within the $this->getExtensionFunctionCalls() return array. Each function name will be used without the 'call'prefix and
     * ucfirst.
     */
    public function callResend($item)
    {
        if (InvitationWorkflow::changeInvitationStatusByName($item, 'resent')) {
            return $this->responseRepository->make('USER_INVITATION_RESENT');
        }
        return $this->responseRepository->make('USER_INVITATION_RESENT_FAILURE');
    }

    public function callCancel($item)
    {
        if (InvitationWorkflow::changeInvitationStatusByName($item, 'canceled')) {
            return $this->responseRepository->make('USER_INVITATION_CANCELED');
        }
        return $this->responseRepository->make('USER_INVITATION_CANCELED_FAILURE');
    }
}