<?php

namespace Photon\PhotonCms\Dependencies\AdditionalModuleClasses\Workflows;

use Photon\PhotonCms\Core\AdditionalModuleClasses\Workflows\InvitationWorkflow as CoreInvitationWorkflow;
use Photon\PhotonCms\Dependencies\DynamicModels\Invitations;
use Photon\PhotonCms\Dependencies\DynamicModels\InvitationStatuses;

/**
 * This class extends the core invitation workflow class.
 * It is primarily used for customizing invitation workflow by user without having to modify any of the core class
 * User can overwrite any method from core Photon\PhotonCms\Core\AdditionalModuleClasses\Workflows\InvitationWorkflow within this class and add custom functionalities to it
 */
class InvitationWorkflow extends CoreInvitationWorkflow
{

    /**
     * Changes invitation status by invitation status system name.
     *
     * This is the main function. It will change a status of an invitation if the workflow permits it, or if we want to force it.
     * Chaning of the status will be followed by all other additional workflows, like emailing, date update, etc.
     * If the workflow doesn't permit the status to be changed, and we didn't force it, false will be returned.
     *
     * @param Invitations $invitation
     * @param string $statusSystemName
     * @param boolean $force
     * @return boolean
     */
    // public static function changeInvitationStatusByName(Invitations $invitation, $statusSystemName, $force = false)
    // {
    //     return parent::changeInvitationStatusByName($invitation, $statusSystemName, $force);
    // }
}