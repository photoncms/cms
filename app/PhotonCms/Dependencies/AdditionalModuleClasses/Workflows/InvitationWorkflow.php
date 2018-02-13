<?php

namespace Photon\PhotonCms\Dependencies\AdditionalModuleClasses\Workflows;

use Carbon\Carbon;
use Photon\PhotonCms\Dependencies\DynamicModels\Invitations;
use Photon\PhotonCms\Dependencies\DynamicModels\InvitationStatuses;
use Photon\PhotonCms\Dependencies\AdditionalModuleClasses\Helpers\InvitationEmailingHelper;

/**
 * This class represents the invitation workflow.
 * It is primarily used for switching the invitation status and applying followup functionality for each status
 * such as sending emails, updating dates, etc.
 *
 * The whole workflow revolves arround changing of the status, and should be extended if needed in this manner.
 */
class InvitationWorkflow
{
    /**
     * Main workflow definition.
     *
     * @var array
     */
    private static $mainWorkflow = [
        'pending' => ['resent', 'canceled', 'used'],
        'resent' => ['resent', 'canceled', 'used'],
        'canceled' => ['resent'],
        'used' => []
    ];

    /**
     * Additional workflow to update dates.
     *
     * @var array
     */
    private static $statusChangeDates = [
        'pending' => ['first_sent'],
        'resent' => ['resent_at'],
        'canceled' => [],
        'used' => []
    ];

    /**
     * Additional emailing workflow.
     *
     * @var array
     */
    private static $emailingFlow = [
        'pending' => ['invitee'],
        'resent' => ['invitee'],
        'canceled' => [],
        'used' => ['invitee'/*, 'administrator'*/]
    ];

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
    public static function changeInvitationStatusByName(Invitations $invitation, $statusSystemName, $force = false)
    {
        $newStatus = InvitationStatuses::whereSystemName($statusSystemName)->first();

        if (!$newStatus) {
            return false;
        }

        $statusChangeAllowed = self::checkIfStatusChangeAllowed($invitation, $newStatus->system_name);

        if ($statusChangeAllowed || $force) {
            $invitation->invitation_status = $newStatus->id;
            self::applyStatusChangeDates($invitation, $newStatus->system_name);
            self::sendStatusChangeEmails($invitation, $newStatus->system_name);
            $invitation->save();
            return true;
        }
        return false;
    }

    /**
     * This is the secondary functionality which checks if the status of an invitation can be changed to another one.
     *
     * @param Invitations $invitation
     * @param string $newStatusSystemName
     * @return boolean
     */
    public static function checkIfStatusChangeAllowed(Invitations $invitation, $newStatusSystemName)
    {
        $currentStatus = $invitation->invitation_status_relation;
        if (!($currentStatus instanceof InvitationStatuses)) {
            return false;
        }

        return in_array($newStatusSystemName, self::$mainWorkflow[$currentStatus->system_name]);
    }

    /**
     * Applies the date update workflow to an invitation depending on the newly requested status.
     *
     * @param Invitations $invitation
     * @param string $newStatusSystemName
     */
    private static function applyStatusChangeDates(Invitations $invitation, $newStatusSystemName)
    {
        $statusChangeDatesForUpdate = self::$statusChangeDates[$newStatusSystemName];

        foreach ($statusChangeDatesForUpdate as $dateForUpdate) {
            $invitation->$dateForUpdate = new Carbon();
        }
    }

    /**
     * Applies the emailing workflow to an invitation depending on the newly requested status.
     *
     * @param Invitations $invitation
     * @param string $newStatusSystemName
     */
    private static function sendStatusChangeEmails(Invitations $invitation, $newStatusSystemName)
    {
        if (in_array('administrator', self::$emailingFlow[$newStatusSystemName])) {
            $methodName = 'email_administrator_'.$newStatusSystemName;
            InvitationEmailingHelper::$methodName($invitation);
        }
        if (in_array('invitee', self::$emailingFlow[$newStatusSystemName])) {
            $methodName = 'email_invitee_'.$newStatusSystemName;
            InvitationEmailingHelper::$methodName($invitation);
        }
    }
}