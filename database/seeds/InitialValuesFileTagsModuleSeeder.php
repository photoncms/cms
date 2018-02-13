<?php

use Illuminate\Database\Seeder;

class InitialValuesFileTagsModuleSeeder extends Seeder
{
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        // Data
        \DB::table('file_tags')->delete();
        \DB::table('file_tags')->insert(array (
            0 =>
            array (
                'id' => 1,
                'title' => 'Profile Image',
                'system_name' => 'profile_image',
                'anchor_text' => 'Profile Image',
                'created_at' => '2016-10-17 00:00:00',
                'updated_at' => '2016-10-17 00:00:00',
                'created_by' => 1,
                'updated_by' => 1,
            ),
        ));
        \Schema::enableForeignKeyConstraints();
    }
}
