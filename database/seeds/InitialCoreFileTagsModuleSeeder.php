<?php

use Illuminate\Database\Seeder;

class InitialCoreFileTagsModuleSeeder extends Seeder
{
    public function run()
    {
        \DB::table('modules')->insert(array (
            29 =>
            array (
                'id' => 7,
                'category' => NULL,
                'type' => 'non_sortable',
                'name' => 'File Tags',
                'model_name' => 'FileTags',
                'table_name' => 'file_tags',
                'anchor_text' => '{{title}}',
                'icon' => 'fa fa-tags',
                'reporting' => 0,
                'lazy_loading' => 0,
                'is_system' => 1,
                'created_at' => '2016-10-07 07:14:34',
                'updated_at' => '2016-10-07 07:14:34',
            ),
        ));

        // Fields
        \DB::table('fields')->insert(array (
            123 =>
            array (
                'type' => 1,
                'name' => 'File tag name',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'title',
                'virtual_name' => NULL,
                'tooltip_text' => 'This is the human-readable file tag name.',
                'validation_rules' => 'required|string|unique:file_tags,title',
                'module_id' => 7,
                'order' => 0,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 1,
                'virtual' => 0,
                'lazy_loading' => 0,
                'is_default_search_choice' => 1,
                'created_at' => '2016-10-07 07:14:34',
                'updated_at' => '2016-10-07 07:14:34',
            ),
            124 =>
            array (
                'type' => 1,
                'name' => 'System tag name',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'system_name',
                'virtual_name' => NULL,
                'tooltip_text' => 'This is the tag system name.',
                'validation_rules' => 'unique:file_tags,system_name',
                'module_id' => 7,
                'order' => 1,
                'editable' => 0,
                'disabled' => 0,
                'hidden' => 1,
                'is_system' => 1,
                'virtual' => 0,
                'lazy_loading' => 0,
                'is_default_search_choice' => 0,
                'created_at' => '2016-10-07 07:14:34',
                'updated_at' => '2016-10-07 07:14:34',
            ),
        ));
    }
}
