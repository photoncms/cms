<?php

use Illuminate\Database\Seeder;

class InitialCoreFieldTypesSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('field_types')->delete();
        \DB::table('field_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'type' => 'input_text',
                'title' => 'Input Text',
                'laravel_type' => 'string',
                'is_system' => 0,
            ),
            1 =>
            array (
                'id' => 2,
                'type' => 'rich_text',
                'title' => 'Rich Text',
                'laravel_type' => 'text',
                'is_system' => 0,
            ),
            2 =>
            array (
                'id' => 3,
                'type' => 'image',
                'title' => 'Image',
                'laravel_type' => 'string',
                'is_system' => 0,
            ),
            3 =>
            array (
                'id' => 4,
                'type' => 'boolean',
                'title' => 'Boolean',
                'laravel_type' => 'boolean',
                'is_system' => 0,
            ),
            4 =>
            array (
                'id' => 5,
                'type' => 'date',
                'title' => 'Date',
                'laravel_type' => 'date',
                'is_system' => 0,
            ),
            6 =>
            array (
                'id' => 7,
                'type' => 'many_to_one',
                'title' => 'Many to One',
                'laravel_type' => 'integer',
                'is_system' => 0,
            ),
            7 =>
            array (
                'id' => 8,
                'type' => 'many_to_many',
                'title' => 'Many to Many',
                'laravel_type' => '',
                'is_system' => 0,
            ),
            8 =>
            array (
                'id' => 9,
                'type' => 'password',
                'title' => 'Password',
                'laravel_type' => 'string',
                'is_system' => 0,
            ),
            9 =>
            array (
                'id' => 10,
                'type' => 'integer',
                'title' => 'Integer',
                'laravel_type' => 'integer',
                'is_system' => 0,
            ),
            10 =>
            array (
                'id' => 11,
                'type' => 'system_integer',
                'title' => 'System Integer',
                'laravel_type' => 'integer',
                'is_system' => 1,
            ),
            11 =>
            array (
                'id' => 12,
                'type' => 'system_date_time',
                'title' => 'System Date-time',
                'laravel_type' => 'datetime',
                'is_system' => 1,
            ),
            12 =>
            array (
                'id' => 13,
                'type' => 'system_string',
                'title' => 'System String',
                'laravel_type' => 'string',
                'is_system' => 1,
            ),
            13 =>
            array (
                'id' => 14,
                'type' => 'one_to_one',
                'title' => 'One to One',
                'laravel_type' => 'integer',
                'is_system' => 0,
            ),
            14 =>
            array (
                'id' => 15,
                'type' => 'asset',
                'title' => 'Asset',
                'laravel_type' => 'string',
                'is_system' => 0,
            ),
            15 =>
            array (
                'id' => 16,
                'type' => 'assets',
                'title' => 'Assets',
                'laravel_type' => '',
                'is_system' => 0,
            ),
            16 =>
            array (
                'id' => 17,
                'type' => 'one_to_many',
                'title' => 'One To Many',
                'laravel_type' => '',
                'is_system' => 0,
            ),
            17 =>
            array (
                'id' => 18,
                'type' => 'date_time',
                'title' => 'Date-time',
                'laravel_type' => 'datetime',
                'is_system' => 0,
            ),
            18 =>
            array (
                'id' => 19,
                'type' => 'gallery',
                'title' => 'Gallery',
                'laravel_type' => 'integer',
                'is_system' => 0,
            ),

            array (
                'id' => 20,
                'type' => 'many_to_one_extended',
                'title' => 'Many to One Extended',
                'laravel_type' => 'integer',
                'is_system' => 0,
            ),

            array (
                'id' => 21,
                'type' => 'many_to_many_extended',
                'title' => 'Many to Many Extended',
                'laravel_type' => '',
                'is_system' => 0,
            ),

            array (
                'id' => 22,
                'type' => 'one_to_one_extended',
                'title' => 'One to One Extended',
                'laravel_type' => 'integer',
                'is_system' => 0,
            ),

            array (
                'id' => 23,
                'type' => 'one_to_many_extended',
                'title' => 'One To Many Extended',
                'laravel_type' => '',
                'is_system' => 0,
            ),

            array (
                'id' => 24,
                'type' => 'permissions',
                'title' => 'Permissions',
                'laravel_type' => '',
                'is_system' => 0,
            )
        ));

    }
}
