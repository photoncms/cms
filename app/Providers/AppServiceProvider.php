<?php

namespace Photon\Providers;

use Illuminate\Support\ServiceProvider;

use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\Module\ModuleGateway;
use Photon\PhotonCms\Core\Entities\Module\ModuleGatewayReport;
use Photon\PhotonCms\Core\Entities\Field\Contracts\FieldGatewayInterface;
use Photon\PhotonCms\Core\Entities\Field\FieldGateway;
use Photon\PhotonCms\Core\Entities\Field\FieldGatewayReport;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationGatewayInterface;
use Photon\PhotonCms\Core\Entities\Migration\MigrationGateway;
use Photon\PhotonCms\Core\Entities\Migration\MigrationGatewayReport;
use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelGatewayInterface;
use Photon\PhotonCms\Core\Entities\Model\ModelGateway;
use Photon\PhotonCms\Core\Entities\Model\ModelGatewayReport;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassGatewayInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassGateway;
use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassGatewayReport;
use Photon\PhotonCms\Core\Entities\Seed\Contracts\SeedGatewayInterface;
use Photon\PhotonCms\Core\Entities\Seed\SeedGateway;
use Photon\PhotonCms\Core\Entities\Seed\SeedGatewayReport;
use Photon\PhotonCms\Core\Entities\Menu\Contracts\MenuGatewayInterface;
use Photon\PhotonCms\Core\Entities\Menu\MenuGateway;
use Photon\PhotonCms\Core\Entities\MenuItem\Contracts\MenuItemGatewayInterface;
use Photon\PhotonCms\Core\Entities\MenuItem\MenuItemGateway;
use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\MenuLinkTypeGatewayInterface;
use Photon\PhotonCms\Core\Entities\MenuLinkType\MenuLinkTypeGateway;

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
        $services = [
            MenuGatewayInterface::class => MenuGateway::class,
            MenuItemGatewayInterface::class => MenuItemGateway::class,
            MenuLinkTypeGatewayInterface::class => MenuLinkTypeGateway::class
        ];
            
        $this->reportingService = $this->app->make('ReportingService');

        if ($this->reportingService->isActive()) {
            $services[ModuleGatewayInterface::class]      = ModuleGatewayReport::class;
            $services[FieldGatewayInterface::class]       = FieldGatewayReport::class;
            $services[MigrationGatewayInterface::class]   = MigrationGatewayReport::class;
            $services[ModelGatewayInterface::class]       = ModelGatewayReport::class;
            $services[NativeClassGatewayInterface::class] = NativeClassGatewayReport::class;
            $services[SeedGatewayInterface::class]        = SeedGatewayReport::class;
        }
        else {
            $services[ModuleGatewayInterface::class]      = ModuleGateway::class;
            $services[FieldGatewayInterface::class]       = FieldGateway::class;
            $services[MigrationGatewayInterface::class]   = MigrationGateway::class;
            $services[ModelGatewayInterface::class]       = ModelGateway::class;
            $services[NativeClassGatewayInterface::class] = NativeClassGateway::class;
            $services[SeedGatewayInterface::class]        = SeedGateway::class;
        }

        foreach ($services as $key => $value) {
            $this->app->bindIf($key, $value);
        }
    }

}
