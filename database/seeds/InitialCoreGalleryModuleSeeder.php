<?php

use Illuminate\Database\Seeder;

class InitialCoreGalleryModuleSeeder extends Seeder
{
    public function run()
    {
        \DB::table('modules')->insert(array (
            0 =>
            array (
                'id' => 12,
                'category' => NULL,
                'type' => 'multilevel_sortable',
                'name' => 'Galleries',
                'model_name' => 'Galleries',
                'table_name' => 'galleries',
                'max_depth' => NULL,
                'slug' => NULL,
                'anchor_text' => '{{id}}',
                'icon' => 'fa fa-camera',
                'reporting' => 0,
                'lazy_loading' => 0,
                'is_system' => 1,
                'created_at' => '2017-08-20 16:32:56',
                'updated_at' => '2017-08-20 16:32:56',
            ),
        ));

        // Fields
        \DB::table('fields')->insert(array (
            0 =>
            array (
                'type' => 1,
                'name' => 'Title',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'title',
                'virtual_name' => NULL,
                'tooltip_text' => 'This is the gallery title.',
                'validation_rules' => '',
                'module_id' => 12,
                'order' => 0,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 0,
                'is_system' => 1,
                'virtual' => 0,
                'lazy_loading' => 0,
                'can_create_search_choice' => 0,
                'is_default_search_choice' => 0,
                'flatten_to_optgroups' => 0,
                'default' => NULL,
                'local_key' => NULL,
                'foreign_key' => NULL,
                'nullable' => 0,
                'indexed' => 0,
                'created_at' => '2017-08-20 16:32:56',
                'updated_at' => '2017-08-20 16:38:43',
            ),
        ));
    }
}
