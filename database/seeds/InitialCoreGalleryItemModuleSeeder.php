<?php

use Illuminate\Database\Seeder;

class InitialCoreGalleryItemModuleSeeder extends Seeder
{
    public function run()
    {
        \DB::table('modules')->insert(array (
            0 =>
            array (
                'id' => 13,
                'category' => 12,
                'type' => 'multilevel_sortable',
                'name' => 'Gallery Items',
                'model_name' => 'GalleryItems',
                'table_name' => 'gallery_items',
                'max_depth' => 0,
                'slug' => NULL,
                'anchor_text' => '{{id}}',
                'icon' => 'fa fa-camera',
                'reporting' => 0,
                'lazy_loading' => 0,
                'is_system' => 1,
                'created_at' => '2017-08-20 16:33:27',
                'updated_at' => '2017-08-20 16:33:27',
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
                'tooltip_text' => 'This is the gallery item title.',
                'validation_rules' => 'nullable|string',
                'module_id' => 13,
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
                'created_at' => '2017-08-20 16:33:27',
                'updated_at' => '2017-08-20 16:33:27',
            ),
            1 =>
            array (
                'type' => 2,
                'name' => 'Description',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'description',
                'virtual_name' => NULL,
                'tooltip_text' => 'This is the gallery item description.',
                'validation_rules' => 'nullable|string',
                'module_id' => 13,
                'order' => 1,
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
                'created_at' => '2017-08-20 16:33:27',
                'updated_at' => '2017-08-20 16:33:27',
            ),
            2 =>
            array (
                'type' => 7,
                'name' => 'Asset',
                'related_module' => 4,
                'relation_name' => 'asset',
                'pivot_table' => NULL,
                'column_name' => NULL,
                'virtual_name' => NULL,
                'tooltip_text' => "This is asset related to this gallery item",
                'validation_rules' => "required|integer|exists:assets,id",
                'module_id' => 13,
                'order' => 2,
                'editable' => 1,
                'disabled' => 0,
                'hidden' => 1,
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
                'created_at' => '2017-08-20 16:38:43',
                'updated_at' => '2017-08-20 16:42:13',
            ),
        ));
    }
}
