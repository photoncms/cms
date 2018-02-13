<?php

namespace Photon\PhotonCms\Core\Entities\MenuLinkType\LinkTypeHandlers;

use App;
use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\LinkTypeHandlerGetDataInterface;

use Photon\PhotonCms\Core\Entities\MenuLinkType\Contracts\LinkTypeHandlerCompileLinkInterface;
use Photon\PhotonCms\Core\Entities\MenuItem\MenuItem;

class AdminPanelSingleEntryHandler extends BaseLinkHandler implements LinkTypeHandlerGetDataInterface, LinkTypeHandlerCompileLinkInterface
{

    protected $type = 'select';
    protected $hasGenericIcon = true;

    /**
     * @var ModuleLibrary
     */
    private $moduleLibrary;

    public function __construct()
    {
        $this->moduleLibrary = App::make('Photon\PhotonCms\Core\Entities\Module\ModuleLibrary');
    }

    /**
     * Retrieves all modules and packs their relevant data to represent this menu type.
     *
     * @return array
     */
    public function getData()
    {
        $modules = $this->moduleLibrary->getAllModules();

        $moduleLinkCollection = [];
        foreach ($modules as $module) {
            $moduleLinkCollection[$module->name] = json_encode([
                'id' => $module->id,
                'table_name' => $module->table_name
            ]);
        }

        return $moduleLinkCollection;
    }

    /**
     * Compiles a link from the provided data for this menu link type.
     *
     * @param string $data
     * @return string
     */
    public function compileLinkFromData($data)
    {
        return "";
    }

    /**
     * Extracts an icon from a menu item.
     *
     * @param MenuItem $menuItem
     * @return string
     */
    public function extractIcon(MenuItem $menuItem)
    {
        if ($menuItem->icon) {
            return $menuItem->icon;
        }
        else {
            $data = $this->parseResourceData($menuItem->resource_data);
            
            $module = $this->moduleLibrary->findByTableName($data['table_name']);

            return $module->icon;
        }
    }

    /**
     * Parses resource data for this menu link type
     *
     * @param string $data
     * @return array
     */
    private function parseResourceData($data)
    {
        return json_decode($data, true);
    }
}