<?php

use Illuminate\Database\Seeder;

class InitialValuesMenusSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('menus')->delete();
        \DB::table('menus')->insert(array (
            0 =>
            array (
                'id' => '1',
                'name' => 'admin_panel_menu',
                'title' => 'Admin Panel Menu',
                'max_depth' => 1,
                'min_root' => null,
                'is_system' => 1,
                'description' => 'This menu is used for the admin panel main left menu.',
                'created_at' => '2016-06-19 20:05:47',
                'updated_at' => '2016-06-19 20:05:47',
                'created_by' => 1,
                'updated_by' => 1
            ),
            1 =>
            array (
                'id' => '2',
                'name' => 'quick_launch_menu',
                'title' => 'Quick Launch Menu',
                'max_depth' => 0,
                'min_root' => null,
                'is_system' => 1,
                'description' => 'Host the items for the Quick Launch Dashboard Menu.',
                'created_at' => '2016-06-19 20:05:47',
                'updated_at' => '2016-06-19 20:05:47',
                'created_by' => 1,
                'updated_by' => 1
            )
        ));

        \DB::table('menu_link_types')->delete();
        \DB::table('menu_link_types')->insert(array (
            0 =>
            array (
                'id' => '1',
                'name' => 'admin_panel_module_link',
                'title' => 'Admin Panel Module Link',
                'clickable' => '1',
                'is_system' => '1',
            ),
            1 =>
            array (
                'id' => '2',
                'name' => 'static_link',
                'title' => 'Static Link',
                'clickable' => '1',
                'is_system' => '1',
            ),
            2 =>
            array (
                'id' => '3',
                'name' => 'menu_item_group',
                'title' => 'Menu Item Group',
                'clickable' => '0',
                'is_system' => '1',
            ),
            3 =>
            array (
                'id' => '4',
                'name' => 'admin_panel_single_entry',
                'title' => 'Admin Panel Single Entry',
                'clickable' => '1',
                'is_system' => '1',
            ),
        ));

        \DB::table('menu_link_types_menus')->delete();
        \DB::table('menu_link_types_menus')->insert(array (
            0 =>
            array (
                'menu_id' => '1',
                'menu_link_type_id' => '1',
            ),
            1 =>
            array (
                'menu_id' => '1',
                'menu_link_type_id' => '3',
            ),
            2 =>
            array (
                'menu_id' => '2',
                'menu_link_type_id' => '1',
            ),
            3 =>
            array (
                'menu_id' => '2',
                'menu_link_type_id' => '3',
            ),
        ));
    }
}
