<?php

use Illuminate\Database\Seeder;

class InitialValuesZClientSeeder extends Seeder
{
    public function run()
    {
        // Image Sizes
        \DB::table('image_sizes')->delete();
        \DB::table('image_sizes')->insert(array (
            array (
                'name' => 'Thumbnail (120x90)',
                'width' => 120,
                'height' => 90,
                'lock_width' => 1,
                'lock_height' => 1,
                'id' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2017-06-14 09:51:17',
                'updated_at' => '2017-06-14 09:51:17',
                'anchor_text' => 'Thumbnail (120x90)',
            ),
            array (
                'name' => 'Large Image (960xauto)',
                'width' => 960,
                'height' => 0,
                'lock_width' => 1,
                'lock_height' => 0,
                'id' => 2,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2018-01-26 20:40:54',
                'updated_at' => '2018-01-26 20:41:08',
                'anchor_text' => 'Large Image (960xauto)',
            ),
        ));
    }
}
