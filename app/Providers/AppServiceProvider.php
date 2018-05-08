<?php

namespace Photon\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        // \DB::listen(function ($query) {
        //     \Log::info(json_encode([
        //         $query->sql,
        //         $query->bindings,
        //         $query->time
        //     ]));
        // });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        
    }

}
