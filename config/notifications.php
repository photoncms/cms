<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application url templates
    |--------------------------------------------------------------------------
    |
    | These are used mainly in \Photon\PhotonCms\Core\Helpers\RoutesHelper and should never be used outside of it.
    | Instead RoutesHelper methods should be used to get to these URLs in a safe decoupled way.
    |
    */
    'url_templates' => [
        'AUTH_CONFIRMATION_URL'         => env('APPLICATION_URL').'/cp/confirm-email/{token}',
        'AUTH_INVITATION_URL'           => env('APPLICATION_URL').'/cp/register/{token}',
        'PASSWORD_RESET_URL'            => env('APPLICATION_URL').'/cp/reset-password/{token}',
        'EMAIL_CHANGE_CONFIRMATION_URL' => env('APPLICATION_URL').'/cp/confirm-email-change/{token}/{user_id}',
        'EXPORTED_FILE_DOWNLOAD_URL'    => env('APPLICATION_URL').'/api/export/download/{file_name}',
        'USER_REVIEW_URL'               => '/cp/admin/users/{user_id}',
        'ABSOLUTE_USER_REVIEW_URL'      => env('APPLICATION_URL').'/cp/admin/users/{user_id}'
    ]
];