<?php

use Illuminate\Database\Seeder;

class ModelMetaDataTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('model_meta_data')->delete();
        
        \DB::table('model_meta_data')->insert(array (
            
            array (
                'module_id' => 1,
                'model_meta_type_id' => 2,
                'value' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            
            array (
                'module_id' => 1,
                'model_meta_type_id' => 3,
                'value' => 'Photon\\PhotonCms\\Core\\Entities\\User\\User as PhotonUser',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            
            array (
                'module_id' => 2,
                'model_meta_type_id' => 2,
                'value' => 'Illuminate\\Notifications\\Notifiable',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            
            array (
                'module_id' => 18371497336724,
                'model_meta_type_id' => 2,
                'value' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            
            array (
                'module_id' => 18371497336724,
                'model_meta_type_id' => 3,
                'value' => 'Photon\\PhotonCms\\Core\\Entities\\User\\User as PhotonUser',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}