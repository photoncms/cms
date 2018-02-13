<?php

use Illuminate\Database\Seeder;

class ModelMetaTypesTableSeeder extends Seeder
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
            
            array (
                'id' => 1,
                'system_name' => 'use',
                'title' => 'Uses class',
            ),
            
            array (
                'id' => 2,
                'system_name' => 'trait',
                'title' => 'Uses trait',
            ),
            
            array (
                'id' => 3,
                'system_name' => 'extend',
                'title' => 'Extends',
            ),
            
            array (
                'id' => 4,
                'system_name' => 'implement',
                'title' => 'Implements interface',
            ),
        ));
        
        
    }
}