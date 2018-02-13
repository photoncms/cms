<?php

use Illuminate\Database\Seeder;

class InitialValuesModelMetaDataSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        


        \Schema::disableForeignKeyConstraints();
        \DB::table('model_meta_data')->delete();
        \DB::table('model_meta_data')->insert(array (
            0 => 
            array (
                'module_id' => 1,
                'model_meta_type_id' => 2,
                'value' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'module_id' => 1,
                'model_meta_type_id' => 3,
                'value' => 'Photon\\PhotonCms\\Core\\Entities\\User\\User as PhotonUser',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 =>
            array (
                'module_id' => 2,
                'model_meta_type_id' => 2,
                'value' => 'Illuminate\\Notifications\\Notifiable',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'module_id' => 18371497336724,
                'model_meta_type_id' => 2,
                'value' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'module_id' => 18371497336724,
                'model_meta_type_id' => 3,
                'value' => 'Photon\\PhotonCms\\Core\\Entities\\User\\User as PhotonUser',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \Schema::enableForeignKeyConstraints();
        
        
    }
}