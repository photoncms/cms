<?php

namespace Photon\PhotonCms\Core\Database\Seeds\InitialCore;

use Illuminate\Database\Seeder;

class FieldGroupsModuleSeeder extends Seeder
{
    public function run()
    {
        // Modules
        \DB::table('modules')->insert(array (
            0 =>
            array (
                'id' => 14,
                'category' => 0,
                'type' => 'sortable',
                'name' => 'Field Groups',
                'model_name' => 'FieldGroups',
                'table_name' => 'field_groups',
                'max_depth' => NULL,
                'slug' => NULL,
                'anchor_text' => '{{|findModule}} - {{name}}',
                'anchor_html' => NULL,
                'icon' => 'fa fa-square',
                'reporting' => 0,
                'lazy_loading' => 0,
                'is_system' => 0,
                'created_at' => '2018-01-10 22:23:31',
                'updated_at' => '2018-01-11 08:18:51',
            ),
        ));

        // Fields
        \DB::table('fields')->insert(array (
            array (
                'type' => 1,
                'name' => 'Name',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'name',
                'virtual_name' => NULL,
                'tooltip_text' => 'Field group name.',
                'validation_rules' => 'required',
                'module_id' => 14,
                'order' => 0,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 0,
                'virtual' => 0,
                'lazy_loading' => 0,
                'can_create_search_choice' => 0,
                'is_default_search_choice' => 0,
                'active_entry_filter' => NULL,
                'flatten_to_optgroups' => 0,
                'default' => NULL,
                'local_key' => NULL,
                'foreign_key' => NULL,
                'nullable' => 1,
                'indexed' => 0,
                'created_at' => '2018-01-10 22:23:31',
                'updated_at' => '2018-01-11 08:18:51',
            ),

            array (
                'type' => 25,
                'name' => 'Module',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'module_id',
                'virtual_name' => NULL,
                'tooltip_text' => NULL,
                'validation_rules' => 'required|exists:modules,id',
                'module_id' => 14,
                'order' => 1,
                'editable' => 0,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 0,
                'virtual' => 0,
                'lazy_loading' => 0,
                'can_create_search_choice' => 0,
                'is_default_search_choice' => 0,
                'active_entry_filter' => NULL,
                'flatten_to_optgroups' => 0,
                'default' => NULL,
                'local_key' => NULL,
                'foreign_key' => NULL,
                'nullable' => 1,
                'indexed' => 0,
                'created_at' => '2018-01-10 22:23:56',
                'updated_at' => '2018-01-11 08:18:51',
            ),
        ));
    }
}
