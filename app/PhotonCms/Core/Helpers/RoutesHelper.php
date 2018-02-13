<?php

namespace Photon\PhotonCms\Core\Helpers;

class RoutesHelper
{

    /**
     * Compiles the application URL.
     *
     * @return string
     */
    public static function getApplicationUrl()
    {
        return env('APPLICATION_URL') . "/cp";
    }

    /**
     * Compiles a confirmation URL for the newly registered user.
     *
     * @param string $token
     * @param string $url
     * @return string
     */
    public static function getAuthConfirmationUrl($token, $url=null)
    {
        if (!$url) {
            // Use a URL template from the notifications config file.
            $url = config('notifications.url_templates.AUTH_CONFIRMATION_URL');
        }

        $url = str_replace('{token}', $token, $url);

        return $url;
    }

    /**
     * Compiles a email change confirmation URL for the specified user.
     *
     * @param int $userId
     * @param string $token
     * @param string $url
     * @return string
     */
    public static function getEmailChangeConfirmationUrl($userId, $token, $url=null)
    {
        if (!$url) {
            // Use a URL template from the notifications config file.
            $url = config('notifications.url_templates.EMAIL_CHANGE_CONFIRMATION_URL');
        }

        $url = str_replace('{user_id}', $userId, $url);
        $url = str_replace('{token}', $token, $url);

        return $url;
    }

    /**
     * Compiles an invitation URL.
     *
     * @param string $token
     * @param string $url
     * @return string
     */
    public static function getAuthInvitationUrl($token, $url=null)
    {
        if (!$url) {
            // Use a URL template from the notifications config file.
            $url = config('notifications.url_templates.AUTH_INVITATION_URL');
        }

        $url = str_replace('{token}', $token, $url);

        return $url;
    }

    /**
     * Compiles a password reset URL.
     *
     * @param string $token
     * @param string $url
     * @return string
     */
    public static function getPasswordResetUrl($token, $url=null)
    {
        if (!$url) {
            // Use a URL template from the notifications config file.
            $url = config('notifications.url_templates.PASSWORD_RESET_URL');
        }

        $url = str_replace('{token}', $token, $url);

        return $url;
    }

    /**
     * Generates a download URL for the specified exported file.
     *
     * @param int $fileName
     * @param string $url
     * @return string
     */
    public static function getExportedFileDownloadUrl($fileName, $url = null)
    {
        if (!$url) {
            // Use a URL template from the notifications config file.
            $url = config('notifications.url_templates.EXPORTED_FILE_DOWNLOAD_URL');
        }

        $url = str_replace('{file_name}', $fileName, $url);

        return $url;
    }

    /**
     * Compiles a new user review URL.
     *
     * @param \Photon\PhotonCms\Dependencies\DynamicModels\User $user
     * @param string $url
     * @return string
     */
    public static function getNewUserReviewUrl(\Photon\PhotonCms\Dependencies\DynamicModels\User $user, $url=null)
    {
        if (!$url) {
            // Use a URL template from the notifications config file.
            $url = config('notifications.url_templates.USER_REVIEW_URL');
        }

        $url = str_replace('{user_id}', $user->id, $url);

        return $url;
    }

    /**
     * Compiles an absolute new user review URL.
     *
     * @param \Photon\PhotonCms\Dependencies\DynamicModels\User $user
     * @param string $url
     * @return string
     */
    public static function getAbsoluteNewUserReviewUrl(\Photon\PhotonCms\Dependencies\DynamicModels\User $user, $url=null)
    {
        if (!$url) {
            // Use a URL template from the notifications config file.
            $url = config('notifications.url_templates.ABSOLUTE_USER_REVIEW_URL');
        }

        $url = str_replace('{user_id}', $user->id, $url);

        return $url;
    }
}