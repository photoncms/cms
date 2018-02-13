<?php

namespace Photon\PhotonCms\Dependencies\AdditionalModuleClasses\Helpers;

use Photon\PhotonCms\Dependencies\DynamicModels\Invitations;

use Photon\PhotonCms\Dependencies\Notifications\InvitationIssued;
use Photon\PhotonCms\Dependencies\Notifications\RegistrationSuccess;

/**
 * This helper class contains all emailing functionality for user invitations.
 */
class InvitationEmailingHelper
{

    /**
     * Emails the invited user with his invitation URL.
     *
     * @param Invitations $invitation
     */
    public static function email_invitee_pending (Invitations $invitation)
    {
        $invitation->notify(new InvitationIssued($invitation));
    }

    /**
     * Emails an invited user with his resent invitation.
     *
     * @param Invitations $invitation
     */
    public static function email_invitee_resent (Invitations $invitation)
    {
        $invitation->notify(new InvitationIssued($invitation));
    }

    /**
     * Emails the invited user that the invitation has been canceled
     *
     * @param Invitations $invitation
     */
    public static function email_invitee_canceled (Invitations $invitation)
    {
        // ToDo: make (Sasa|09/2016)
    }

    /**
     * Email the invited user that the invitation has been used.
     *
     * @param Invitations $invitation
     */
    public static function email_invitee_used (Invitations $invitation)
    {
        // $invitation->notify(new RegistrationSuccess($invitation));
    }
}