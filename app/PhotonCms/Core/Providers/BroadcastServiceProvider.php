<?php

namespace Photon\PhotonCms\Core\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Photon\PhotonCms\Core\Entities\User\User as PhotonUser;

class BroadcastServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        // Register authentication route
        Broadcast::routes(['middleware' => 'broadcasting']);

        // Authenticate the user's personal channels
        Broadcast::channel('Photon.PhotonCms.Dependencies.DynamicModels.User.{userId}', function (PhotonUser $user, $userId) {
            return $user->id == $userId;
        });
    }
}