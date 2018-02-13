<?php

use Illuminate\Database\Seeder;

class InitialValuesUsersModuleSeeder extends Seeder
{
    public function run()
    {
        // Data
        \DB::table('users')->delete();
        \DB::table('users')->insert(array (
            array (
                'id' => 1,
                'email' => 'super.administrator@photoncms.test',
                'password' => '$2y$10$3dJgY8IVSdpnckheZyg5rOfgeMQwQly810WvPzuM41SGL33W8KRfy',
                'confirmed' => 1,
                'confirmation_code' => NULL,
                'first_name' => 'Super',
                'last_name' => 'Administrator',
                'anchor_text' => 'Super Administrator',
                'profile_image' => NULL,
                'password_created_at' => '2018-01-01 00:00:00',
                'deleted_at' => NULL,
                'created_at' => '2016-03-30 11:43:28',
                'updated_at' => '2016-10-21 18:34:50',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            array (
                'id' => 2,
                'email' => 'administrator@photoncms.test',
                'password' => '$2y$10$3dJgY8IVSdpnckheZyg5rOfgeMQwQly810WvPzuM41SGL33W8KRfy',
                'confirmed' => 1,
                'confirmation_code' => NULL,
                'first_name' => 'Administrator',
                'last_name' => '',
                'anchor_text' => 'Administrator',
                'profile_image' => NULL,
                'password_created_at' => '2018-01-01 00:00:00',
                'deleted_at' => NULL,
                'created_at' => '2016-03-30 11:43:28',
                'updated_at' => '2016-10-21 18:34:50',
                'created_by' => 1,
                'updated_by' => 1,
            ),
        ));

        // Role relations
        \DB::table('user_has_roles')->delete();
        \DB::table('user_has_roles')->insert(array (
            array (
                'user_id' => '1',
                'role_id' => '1',
            ),
            array (
                'user_id' => '2',
                'role_id' => '2',
            )
        ));
    }
}
