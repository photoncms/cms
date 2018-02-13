<?php

use Illuminate\Database\Seeder;

class InitialValuesModuleTypesSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        \DB::table('module_types')->delete();
        \DB::table('module_types')->insert(array (
            0 => 
            array (
                'id' => '1',
                'type' => 'single_entry',
                'title' => 'Single Entry',
            ),
            1 => 
            array (
                'id' => '2',
                'type' => 'non_sortable',
                'title' => 'Non Sortable',
            ),
            2 => 
            array (
                'id' => '3',
                'type' => 'sortable',
                'title' => 'Sortable',
            ),
            3 => 
            array (
                'id' => '4',
                'type' => 'multilevel_sortable',
                'title' => 'Multilevel Sortable',
            ),
        ));
        
    }
}
