<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::get("/ping-home", "PhotonController@pingHome")->middleware("throttle:60,1");


Route::get('/export/download/{fileName}', 'DynamicModuleController@downloadExport');


//---------- Photon Core -----------/
// Route::group(['prefix' => '/photon'], function () {
    //Fully restarts the photon installation.
    // Route::get('/hard_reset', 'PhotonController@hardReset');
    // Route::get('/soft_reset', 'PhotonController@softReset');
// });
//---------- End of Photon Core -----------/

Route::group(['middleware' => 'checkLicense'], function () {
    Route::group(['middleware' => 'throttle_protected'], function () {
        Route::post('/auth/login', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@authenticate');
        Route::get('/auth/confirm/{confirmationCode}', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@confirm');
        Route::post('/password/request_reset', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@requestResetPassword');
        Route::post('/password/reset', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@resetPassword');
    });

    if(env("CAN_REGISTER_USER", true)) {
        Route::post('/auth/register', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@register');
    }
    Route::post('/auth/register/{invitationCode}', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@registerWithInvitation');

    Route::group(['middleware' => 'adminpanel'], function () {
        
        //---------- Photon Core -----------/
        Route::group(['prefix' => '/photon'], function () {
            //Rebulids the module model.
            Route::get('/rebuild_model/{tableName}', 'PhotonController@rebuildModuleModel')->middleware('isSuperAdmin');
            Route::get('/rebuild_extender/{tableName}', 'PhotonController@rebuildModuleExtender')->middleware('isSuperAdmin');
            //Photon Sync
            Route::get('/sync', 'PhotonController@sync')->middleware('isSuperAdmin');
            Route::get('/revert_to_sync', 'PhotonController@revertToSync')->middleware('isSuperAdmin');
            Route::get('/revert_to_sync/{tableName}', 'PhotonController@revertModuleToSync')->middleware('isSuperAdmin');
        });

        //---------- User Authentication -----------/
        Route::group(['middleware' => 'throttle_protected'], function () {
            Route::post('/password/change', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@changePassword');
        });
        Route::get('/auth/refresh', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@refreshToken');
        Route::get('/auth/logout', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@logout');
        Route::get('/auth/me', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@getAuthenticatedUser');

        Route::get('/auth/impersonate/stop', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@stopImpersonating');
        Route::get('/auth/impersonate/{id}', '\Photon\PhotonCms\Core\Controllers\auth\JwtAuthController@startImpersonating')->middleware('isSuperAdmin');
        //---------- End of User Administration -----------/

        //---------- Menu Link Types -----------/
        Route::group(['prefix' => '/menus/link_types'], function () {

            //Returns a list of available menu link types.
            Route::get('/', 'MenuLinkTypeController@getMenuLinkTypes');

            //Returns a list of compiled resources for a specific link type.
            // Route::get('/resources/{type}', 'MenuLinkTypeController@getMenuLinkTypeResources');
        });
        //---------- End of Menu Link Types -----------/

        //---------- Menu Items -----------/
        Route::group(['prefix' => '/menus/items'], function () {
            //Creates a new menu entry.
            Route::post('/', 'MenuItemController@createMenuItem')->middleware('isSuperAdmin');

            //Repositions a menu entry.
            Route::put('/reposition', 'MenuItemController@repositionMenuItem')->middleware('isSuperAdmin');

            //Retrieves a menu entry.
            Route::get('/{itemId}', 'MenuItemController@getMenuItem');

            //Updates a menu entry.
            Route::put('/{itemId}', 'MenuItemController@updateMenuItem')->middleware('isSuperAdmin');

            //Deletes a menu item by a slug name or id.
            Route::delete('/{itemId}', 'MenuItemController@deleteMenuItem')->middleware('isSuperAdmin');

            //Returns ancestors of menu item
            Route::get('/{itemId}/ancestors', 'MenuItemController@getMenuItemAncestors');
        });
        //---------- End of Menu Items -----------/

        //---------- Menus -----------/
        Route::group(['prefix' => '/menus'], function () {

            //Gets all menus.
            Route::get('/', 'MenuController@getMenus');

            //Returns list of menu items for the specified menu.
            Route::get('/{menuId}/items', 'MenuItemController@getMenuItems');

            //Returns a list of available menus.
            Route::get('/{menuId}', 'MenuController@getMenu');

            //Creates a new menu.
            Route::post('/', 'MenuController@createMenu')->middleware('isSuperAdmin');

            //Updates an existing menu.
            Route::put('/{menuId}', 'MenuController@updateMenu')->middleware('isSuperAdmin');

            //Deletes a menu.
            Route::delete('/{menuId}', 'MenuController@deleteMenu')->middleware('isSuperAdmin');
        });
        //---------- End of Menus -----------/

        //---------- Generator -----------/
        Route::group(['prefix' => '/modules'], function () {
            //Returns a list of modules.
            Route::get('/', 'ModuleController@getAllModules');

            //Creates a new module.
            Route::post('/', 'ModuleController@createModule')->middleware('isSuperAdmin');

            //Updates a module.
            Route::put('/{table}', 'ModuleController@updateModule')->middleware('isSuperAdmin');

            //Returns module information.
            Route::get('/{table}', 'ModuleController@getModule');

            //Deletes a module.
            Route::delete('/{table}', 'ModuleController@deleteModule')->middleware('isSuperAdmin');
        });
        //---------- End of Generator -----------/

        //---------- Generator -----------/
        Route::group(['prefix' => '/module_types'], function () {
            //Returns a list of modules.
            Route::get('/', 'ModuleTypeController@getAllModuleTypes');
        });
        //---------- End of Generator -----------/

        //---------- Generator -----------/
        Route::group(['prefix' => '/field_types'], function () {
            //Returns a list of modules.
            Route::get('/', 'FieldTypeController@getAllFieldTypes');
        });
        //---------- End of Generator -----------/

        //---------- Nodes -----------/
        Route::group(['prefix' => '/nodes'], function () {
            //Returns child nodes of the given node.
            Route::get('/{tableName}/{id}', 'NodeController@getNodeChildren');

            //Returns child nodes of given node.
            Route::get('/{tableName}/ancestors/{id}', 'NodeController@getNodeAncestors');

            //Returns a list of root items of given `table_name`.
            Route::get('/{tableName}', 'NodeController@getNodeChildren');

            //Repositions nodes.
            Route::put('/reposition', 'NodeController@repositionNode');
        });
        //---------- End of Nodes -----------/

        //---------- Modules extension calls -----------/
        //Returns a list of root items of given `table_name`.
        Route::get('/extension_call/{tableName}/{entryId}/{action}/{parameters?}', 'DynamicModuleController@callExtension')->where('parameters', '(.*)');
        //---------- End of Modules extension calls -----------/

        //---------- Outside notifications -----------/
        //Returns a list of root items of given `table_name`.
        Route::post('/notify/{notification_name}', 'NotificationController@notify');

        Route::get('/notifications/read/{notificationId}', 'NotificationController@read');

        Route::get('/notifications/unread/count', 'NotificationController@countUnreadNotifications');

        Route::post('/notifications/all', 'NotificationController@getNotifications');

        Route::post('/notifications/unread', 'NotificationController@getUnreadNotifications');

        Route::get('/notifications/fcm/assign_token/{token}', 'NotificationController@assignFCMToken');

        Route::get('/notifications/fcm/revoke_token/{token}', 'NotificationController@revokeFCMToken');
        //---------- End of Outside notifications -----------/

        //---------- Module Subscriptions ------------------/
        Route::post('/subscribe/{tableName}/{entryId}', 'DynamicModuleController@insertSubscription');
        Route::delete('/subscribe/{tableName}/{entryId}', 'DynamicModuleController@deleteSubscription');
        //---------- End of Module Subscriptions -----------/

        //---------- Modules -----------/
        Route::get('/{tableName}', 'DynamicModuleController@getAllEntries');

        //Returns single module item (e.g. news).
        Route::get('/{tableName}/{entryId}', 'DynamicModuleController@getEntry')->where('entryId', '[0-9]+');

        //Creates a new module item (e.g. new news).
        Route::post('/{tableName}', 'DynamicModuleController@insertEntry');

        //Updates a single module item (e.g. news/1).
        Route::put('/{tableName}/{id}', 'DynamicModuleController@updateEntry');

        //Mass updates module items.
        Route::put('/{tableName}', 'DynamicModuleController@massUpdate');

        //Deletes a single module item (e.g. news/1).
        Route::delete('/{tableName}/{entryId}', 'DynamicModuleController@deleteEntry')->where('entryId', '[0-9]+');

        //Filters module entries.
        Route::post('/filter/{tableName}', 'DynamicModuleController@getAllEntries');

        // Exports all matching module entries. Uses filter.
        Route::post('/export/{tableName}', 'DynamicModuleController@exportEntries');

        // Exports a single module entry.
        Route::post('/export/{tableName}/{entryId}', 'DynamicModuleController@exportEntry');

        //Counts module entries.
        Route::post('/count/{tableName}', 'DynamicModuleController@countAllEntries');

        //---------- End of Modules -----------/
    });
});
