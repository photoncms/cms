<?php

use Illuminate\Database\Seeder;

class InitialCorePermissionPermissionsModuleSeeder extends Seeder
{
    public function run()
    {
        // Modules
        \DB::table('modules')->insert(array (
            9 =>
            array (
                'id' => 6,
                'category' => NULL,
                'type' => 'non_sortable',
                'name' => 'Permissions',
                'model_name' => 'Permissions',
                'table_name' => 'permissions',
                'anchor_text' => '{{title}}',
                'icon' => 'fa fa-minus-circle',
                'reporting' => 0,
                'lazy_loading' => 0,
                'is_system' => 1,
                'created_at' => '2016-09-02 12:43:44',
                'updated_at' => '2016-09-02 12:43:44',
            ),
        ));

        // Fields
        \DB::table('fields')->insert(array (
            25 =>
            array (
                'type' => 1,
                'name' => 'System Name',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'name',
                'virtual_name' => NULL,
                'tooltip_text' => 'The permission system name.',
                'validation_rules' => 'required|unique:permissions',
                'module_id' => 6,
                'order' => 0,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 1,
                'virtual' => 0,
                'lazy_loading' => 0,
                'default' => NULL,
                'nullable' => 0,
                'created_at' => '2016-09-02 12:43:44',
                'updated_at' => '2016-09-02 12:43:44',
            ),
            26 =>
            array (
                'type' => 1,
                'name' => 'Title',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'title',
                'virtual_name' => NULL,
                'tooltip_text' => 'The human-readable permission name.',
                'validation_rules' => '',
                'module_id' => 6,
                'order' => 1,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 1,
                'virtual' => 0,
                'lazy_loading' => 0,
                'default' => NULL,
                'nullable' => 1,
                'created_at' => '2016-09-02 12:43:44',
                'updated_at' => '2016-09-02 12:43:44',
            ),
        ));
    }
}
