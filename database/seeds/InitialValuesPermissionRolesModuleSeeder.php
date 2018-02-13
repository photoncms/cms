<?php

use Illuminate\Database\Seeder;

class InitialValuesPermissionRolesModuleSeeder extends Seeder
{
    public function run()
    {
        // Data
        \Schema::disableForeignKeyConstraints();

        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'super_administrator',
                'title' => 'Super Administrator',
                'anchor_text' => 'Super Administrator',
                'created_at' => '2016-09-05 13:54:39',
                'updated_at' => '2016-09-05 13:54:39',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'administrator',
                'title' => 'Administrator',
                'anchor_text' => 'Administrator',
                'created_at' => '2016-09-05 13:54:39',
                'updated_at' => '2016-09-05 13:54:39',
                'created_by' => 1,
                'updated_by' => 1,
            ),
        ));

        \DB::table('permissions')->delete();

        \DB::table('permissions')->insert(array (
            0 =>
            array (
                'name' => 'modify_module:users',
                'title' => 'Can Modify \'users\' Module',
                'id' => 1,
                'created_at' => '2018-01-30 23:08:07',
                'updated_at' => '2018-01-30 23:08:07',
                'anchor_text' => 'Can Modify \'users\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            1 =>
            array (
                'name' => 'modify_module:invitations',
                'title' => 'Can Modify \'invitations\' Module',
                'id' => 2,
                'created_at' => '2018-01-30 23:08:16',
                'updated_at' => '2018-01-30 23:08:16',
                'anchor_text' => 'Can Modify \'invitations\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            2 =>
            array (
                'name' => 'modify_module:invitation_statuses',
                'title' => 'Can Modify \'invitation_statuses\' Module',
                'id' => 3,
                'created_at' => '2018-01-30 23:08:27',
                'updated_at' => '2018-01-30 23:08:27',
                'anchor_text' => 'Can Modify \'invitation_statuses\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            3 =>
            array (
                'name' => 'modify_module:roles',
                'title' => 'Can Modify \'roles\' Module',
                'id' => 4,
                'created_at' => '2018-01-30 23:08:38',
                'updated_at' => '2018-01-30 23:08:38',
                'anchor_text' => 'Can Modify \'roles\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            4 =>
            array (
                'name' => 'modify_module:permissions',
                'title' => 'Can Modify \'permissions\' Module',
                'id' => 5,
                'created_at' => '2018-01-30 23:08:57',
                'updated_at' => '2018-01-30 23:08:57',
                'anchor_text' => 'Can Modify \'permissions\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            5 =>
            array (
                'name' => 'modify_module:resized_images',
                'title' => 'Can Modify \'resized_images\' Module',
                'id' => 6,
                'created_at' => '2018-01-30 23:09:09',
                'updated_at' => '2018-01-30 23:09:09',
                'anchor_text' => 'Can Modify \'resized_images\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            6 =>
            array (
                'name' => 'modify_module:image_sizes',
                'title' => 'Can Modify \'image_sizes\' Module',
                'id' => 7,
                'created_at' => '2018-01-30 23:09:24',
                'updated_at' => '2018-01-30 23:09:24',
                'anchor_text' => 'Can Modify \'image_sizes\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            7 =>
            array (
                'name' => 'cannot_edit_field:users:roles',
                'title' => 'Cannot Edit \'users\' Module \'roles\' Field',
                'id' => 8,
                'created_at' => '2018-01-30 23:25:53',
                'updated_at' => '2018-01-30 23:25:53',
                'anchor_text' => 'Cannot Edit \'users\' Module \'roles\' Field',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            8 =>
            array (
                'name' => 'cannot_edit_field:users:permissions',
                'title' => 'Cannot Edit \'users\' Module \'permissions\' Field',
                'id' => 9,
                'created_at' => '2018-01-30 23:26:10',
                'updated_at' => '2018-01-30 23:26:10',
                'anchor_text' => 'Cannot Edit \'users\' Module \'permissions\' Field',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            9 =>
            array (
                'name' => 'create_entry:users',
                'title' => 'Can \'create\' Entry From \'users\' Module',
                'id' => 10,
                'created_at' => '2018-01-30 23:55:05',
                'updated_at' => '2018-01-30 23:55:05',
                'anchor_text' => 'Can \'create\' Entry From \'users\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            10 =>
            array (
                'name' => 'delete_entry:users',
                'title' => 'Can \'delete\' Entry From \'users\' Module',
                'id' => 11,
                'created_at' => '2018-01-30 23:55:20',
                'updated_at' => '2018-01-30 23:55:20',
                'anchor_text' => 'Can \'delete\' Entry From \'users\' Module',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            11 =>
            array (
                'name' => 'update_module:users_match:id_to:id',
                'title' => 'Cannot \'update\' \'users\' Module, Match \'id\' To \'id\'',
                'id' => 12,
                'created_at' => '2018-01-30 23:56:08',
                'updated_at' => '2018-01-30 23:56:08',
                'anchor_text' => 'Cannot \'update\' \'users\' Module, Match \'id\' To \'id\'',
                'anchor_html' => '',
                'slug' => '',
                'created_by' => 1,
                'updated_by' => 1,
            ),
        ));

        \DB::table('role_has_permissions')->delete();

        \DB::table('role_has_permissions')->insert(array (
            0 =>
            array (
                'role_id' => 2,
                'permission_id' => 8,
            ),
            1 =>
            array (
                'role_id' => 2,
                'permission_id' => 9,
            ),
            2 =>
            array (
                'role_id' => 2,
                'permission_id' => 1,
            ),
            3 =>
            array (
                'role_id' => 2,
                'permission_id' => 12,
            ),
        ));

        \Schema::enableForeignKeyConstraints();
    }
}
