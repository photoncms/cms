<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active mode
    |--------------------------------------------------------------------------
    |
    | Flag that shows weather Photon CMS is being used or not
    |
    */
    'active' => true, // ToDo: this is not used, check if this was something that was supposed to be implemented, if not, delete it (Sasa|06/2017)

    /*
    |--------------------------------------------------------------------------
    | API URI
    |--------------------------------------------------------------------------
    |
    | URL of Photon CMS administration panel
    |
    */
    'uri' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Use slugs
    |--------------------------------------------------------------------------
    |
    | Flag which determines if slugs will be used for all modules.
    |
    */
    'use_slugs' => true,

    /*
    |--------------------------------------------------------------------------
    | Dynamic Models location
    |--------------------------------------------------------------------------
    |
    | Location of photon dynamic models
    |
    */
    'dynamic_models_location' => '/PhotonCms/Dependencies/DynamicModels',

    /*
    |--------------------------------------------------------------------------
    | Dynamic Models namespace
    |--------------------------------------------------------------------------
    |
    | Namespace for dynamic models
    |
    */
    'dynamic_models_namespace' => 'Photon\PhotonCms\Dependencies\DynamicModels',

    /*
    |--------------------------------------------------------------------------
    | Dynamic Models namespace
    |--------------------------------------------------------------------------
    |
    | Namespace for dynamic models
    |
    */
    'dynamic_model_templates_namespace' => 'Photon\PhotonCms\Core\Entities\DynamicModuleModel\ModelTemplateTypes',

    /*
    |--------------------------------------------------------------------------
    | Dynamic Module extenders namespace
    |--------------------------------------------------------------------------
    |
    | Namespace for dynamic module extenders
    |
    */
    'dynamic_module_extenders_location' => '/PhotonCms/Dependencies/ModuleExtensions',

    /*
    |--------------------------------------------------------------------------
    | Dynamic Module extenders namespace
    |--------------------------------------------------------------------------
    |
    | Namespace for dynamic module extenders
    |
    */
    'dynamic_module_extenders_namespace' => 'Photon\PhotonCms\Dependencies\ModuleExtensions',

    /*
    |--------------------------------------------------------------------------
    | Dynamic Models location
    |--------------------------------------------------------------------------
    |
    | Location of photon dynamic models
    |
    */
    'dynamic_module_extensions_namespace' => 'Photon\PhotonCms\Dependencies\ModuleExtensions',

    /*
    |--------------------------------------------------------------------------
    | Dynamic model migrations location
    |--------------------------------------------------------------------------
    |
    | Location of photon module migrations
    |
    */
    'dynamic_model_migrations_dir' => '/app/PhotonCms/Dependencies/PhotonMigrations',

    /*
    |--------------------------------------------------------------------------
    | Dynamic model migrations location
    |--------------------------------------------------------------------------
    |
    | Location of photon module migrations
    |
    */
    'dynamic_module_field_types_dir' => '\Photon\PhotonCms\Dependencies\DynamicModuleFieldTypes\\',

    /*
    |--------------------------------------------------------------------------
    | Dynamic module exporters namespace
    |--------------------------------------------------------------------------
    |
    | Namespace prefix of photon module exporters
    |
    */
    'dynamic_module_exporters_namespace' => 'Photon\PhotonCms\Dependencies\ModuleExporters\\',

    /*
    |--------------------------------------------------------------------------
    | Dynamic module exporters location
    |--------------------------------------------------------------------------
    |
    | Location of photon module exporters
    |
    */
    'dynamic_module_exporters_location' => app_path('PhotonCms/Dependencies/ModuleExporters'),

    /*
    |--------------------------------------------------------------------------
    | Photon jobs directory
    |--------------------------------------------------------------------------
    |
    | Location of photon jobs
    |
    */
    'jobs_dir' => app_path('/PhotonCms/Dependencies/Jobs'),

    /*
    |--------------------------------------------------------------------------
    | Photon jobs namespace
    |--------------------------------------------------------------------------
    |
    | Namespace prefix of photon jobs
    |
    */
    'jobs_namespace' => 'Photon\PhotonCms\Dependencies\Jobs\\',

    /*
    |--------------------------------------------------------------------------
    | Backup PHP seeds location
    |--------------------------------------------------------------------------
    |
    | Location of photon php backup seeds
    |
    */
    'php_seed_backup_location' => app_path('PhotonCms/Dependencies/Backup/PHPSeeds'),

    /*
    |--------------------------------------------------------------------------
    | Error logging path and filename
    |--------------------------------------------------------------------------
    |
    | Location of photon log file
    |
    */
    'error_log' => app_path('PhotonCms/Dependencies/Logging/error.log'),

    /*
    |--------------------------------------------------------------------------
    | Registration service flag
    |--------------------------------------------------------------------------
    |
    | A flag which determines if the registration service is used in
    | registration process. This means, the user is required to confirm their
    | email address.
    |
    */
    'use_registration_service_email' => env('USE_REGISTRATION_SERVICE_EMAIL', true),

    'service_emails' => [
        'registration' => env('REGISTRATION_SERVICE_EMAIL'),
        'reset_password' => env('RESET_PASSWORD_SERVICE_EMAIL'),
        'notification' => env('NOTIFICATION_SERVICE_EMAIL'),
        'invitation' => env('INVITATION_SERVICE_EMAIL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rules for role assignment
    |--------------------------------------------------------------------------
    |
    | Rules for assignment of specific roles.
    |
    */
    'role_assignment_rules' => [
//        [
//            'role' => 'super_administrator',
//            'min' => 1
//        ],
//        [
//            'role' => 'moderator',
//            'min' => 1,
//            'match' => [
//                'administrator'
//            ]
//        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Directories for photon reset cleaning
    |--------------------------------------------------------------------------
    |
    | Directories which are supposed to be cleaned with photon reset.
    |
    */
    'photon_reset_clean_directories' => [
        storage_path('exports')
    ],

    /*
    |--------------------------------------------------------------------------
    | Table names of tables which are emptied during photon sync
    |--------------------------------------------------------------------------
    |
    */
    'photon_sync_clear_tables' => [
        'fields',
        'field_types',
        'modules',
        'module_types',
        'model_meta_data',
        'model_meta_types',  
        'menu_link_types'      
    ],

    /*
    |--------------------------------------------------------------------------
    | Table names of tables which needs to be backed up during photon sync
    |--------------------------------------------------------------------------
    |
    */
    'photon_sync_backup_tables' => [
        'email_change_requests',
        'failed_jobs',
        'menus',
        'menu_items',        
        'menu_link_types_menus',
        'notifications',
        'password_resets',
        'used_passwords'
    ],

    /*
    |--------------------------------------------------------------------------
    | Throttle max tries before lock-out
    |--------------------------------------------------------------------------
    |
    */
    'throttle_max_times' => env('THROTTLE_MAX_TIMES', 5),

    /*
    |--------------------------------------------------------------------------
    | Throttle cooldown time in minutes
    |--------------------------------------------------------------------------
    |
    */
    'throttle_cooldown_minutes' => env('THROTTLE_COOLDOWN_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Exported files TTL in seconds
    |--------------------------------------------------------------------------
    |
    | Expiration time for exported files since their creation, measured in seconds.
    |
    */
    'exported_files_ttl' => env('EXPORTED_FILES_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'uid_seed_string' => env('APP_UID_SEED_STRING', null),

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'imagick_quality' => env('IMAGICK_QUALITY', 85),

    /*
    |--------------------------------------------------------------------------
    | '2x2' corelates to '4:2:0'
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'imagick_sampling' => env('IMAGICK_SAMPLING', '2x2'),

    /*
    |--------------------------------------------------------------------------
    | should photon use caching? if use file or database cache drivers are not supported
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'use_photon_cache' => env('USE_PHOTON_CACHING', false),

    /*
    |--------------------------------------------------------------------------
    | how long should photon cache be stored (in minutes)
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'photon_caching_time' => env('PHOTON_CACHING_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | should anchor fields be automatically updated
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'mass_auto_update_anchor' => env('MASS_AUTO_UPDATE_ANCHOR', true),

    /*
    |--------------------------------------------------------------------------
    | Column names from users module that would be used for generating registration validator
    |--------------------------------------------------------------------------
    |
    */
    'photon_register_use_columns' => [
        'first_name',
        'last_name',
        'email',
        'password'
    ],

];
