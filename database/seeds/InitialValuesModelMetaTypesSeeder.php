<?php

use Illuminate\Database\Seeder;

class InitialValuesModelMetaTypesSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        \DB::table('model_meta_types')->delete();
        \DB::table('model_meta_types')->insert(array (
            0 => 
            array (
                'id' => '1',
                'system_name' => 'use',
                'title' => 'Uses class',
            ),
            1 => 
            array (
                'id' => '2',
                'system_name' => 'trait',
                'title' => 'Uses trait',
            ),
            2 => 
            array (
                'id' => '3',
                'system_name' => 'extend',
                'title' => 'Extends',
            ),
            3 => 
            array (
                'id' => '4',
                'system_name' => 'implement',
                'title' => 'Implements interface',
            ),
        ));
    }
}
