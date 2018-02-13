<?php

use Illuminate\Database\Seeder;

class InitialCorePermissionRolesModuleSeeder extends Seeder
{
    public function run()
    {
        // Modules
        \DB::table('modules')->insert(array (
            7 =>
            array (
                'id' => 5,
                'category' => NULL,
                'type' => 'non_sortable',
                'name' => 'Roles',
                'model_name' => 'Roles',
                'table_name' => 'roles',
                'anchor_text' => '{{title}}',
                'icon' => 'fa fa-user-circle-o',
                'reporting' => 0,
                'lazy_loading' => 0,
                'is_system' => 1,
                'created_at' => '2016-09-02 12:24:28',
                'updated_at' => '2016-09-02 12:24:28',
            ),
        ));

        // Fields
        \DB::table('fields')->insert(array (
            18 =>
            array (
                'type' => 1,
                'name' => 'System Name',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'name',
                'virtual_name' => NULL,
                'tooltip_text' => 'This is the role system name.',
                'validation_rules' => 'required|unique:roles',
                'module_id' => 5,
                'order' => 0,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 1,
                'virtual' => 0,
                'lazy_loading' => 0,
                'created_at' => '2016-09-02 12:24:28',
                'updated_at' => '2016-09-02 12:24:28',
            ),
            19 =>
            array (
                'type' => 1,
                'name' => 'Title',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'title',
                'virtual_name' => NULL,
                'tooltip_text' => 'This is the human-readable role name.',
                'validation_rules' => '',
                'module_id' => 5,
                'order' => 1,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 1,
                'virtual' => 0,
                'lazy_loading' => 0,
                'created_at' => '2016-09-02 12:24:28',
                'updated_at' => '2016-09-02 12:24:28',
            ),
            28 =>
            array (
                'type' => 24,
                'name' => 'Permissions',
                'related_module' => 6,
                'relation_name' => 'permissions',
                'pivot_table' => 'role_has_permissions',
                'column_name' => NULL,
                'virtual_name' => NULL,
                'tooltip_text' => 'Permissions which are assigned to this role.',
                'validation_rules' => NULL,
                'module_id' => 5,
                'order' => 2,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 1,
                'virtual' => 0,
                'lazy_loading' => 0,
                'created_at' => '2016-09-05 10:28:05',
                'updated_at' => '2016-09-05 10:28:05',
            ),
        ));

    }
}
