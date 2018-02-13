<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API location
    |--------------------------------------------------------------------------
    |
    | API location used for testing
    |
    */

    'test_uri' => env('TEST_URI', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Email recipient
    |--------------------------------------------------------------------------
    |
    | Email address to send emails to while testing.
    |
    */

    'test_recipient_email' => env('TEST_RECIPIENT_EMAIL', 'admin@photon.dev'),

    /*
    |--------------------------------------------------------------------------
    | Email recipient
    |--------------------------------------------------------------------------
    |
    | Email address to send emails to while testing.
    |
    */

    'test_admin_user_email' => env('TEST_ADMIN_USER_EMAIL', 'admin@photon.dev'),

    /*
    |--------------------------------------------------------------------------
    | Throttle limit
    |--------------------------------------------------------------------------
    |
    | Set this value to the same throttle value you set you application.
    |
    */

    'test_authentication_throttle' => 5,

];
