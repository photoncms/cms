<?php

use Illuminate\Database\Seeder;

class MenuLinkTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menu_link_types')->delete();
        
        \DB::table('menu_link_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin_panel_module_link',
                'title' => 'Admin Panel Module Link',
                'clickable' => 1,
                'is_system' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'static_link',
                'title' => 'Static Link',
                'clickable' => 1,
                'is_system' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'menu_item_group',
                'title' => 'Menu Item Group',
                'clickable' => 0,
                'is_system' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'admin_panel_single_entry',
                'title' => 'Admin Panel Single Entry',
                'clickable' => 1,
                'is_system' => 1,
            ),
        ));
        
        
    }
}