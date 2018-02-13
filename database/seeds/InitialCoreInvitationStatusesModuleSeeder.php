<?php

use Illuminate\Database\Seeder;

class InitialCoreInvitationStatusesModuleSeeder extends Seeder
{
    public function run()
    {
        // Modules
        \DB::table('modules')->insert(array (
            3 =>
            array (
                'id' => 3,
                'category' => NULL,
                'type' => 'non_sortable',
                'name' => 'Invitation Statuses',
                'model_name' => 'InvitationStatuses',
                'table_name' => 'invitation_statuses',
                'anchor_text' => '{{title}}',
                'icon' => 'fa fa-list',
                'reporting' => false,
                'lazy_loading' => 0,
                'is_system' => true,
                'created_at' => '2016-08-18 12:18:07',
                'updated_at' => '2016-08-18 12:18:07',
            ),
        ));

        // Fields
        \DB::table('fields')->insert(array (
            10 =>
            array (
                'type' => 1,
                'name' => 'Title',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'title',
                'virtual_name' => NULL,
                'tooltip_text' => 'The invitation status title.',
                'validation_rules' => 'string|required',
                'module_id' => 3,
                'order' => 0,
                'editable' => true,
                'disabled' => false,
                'hidden' => false,
                'is_system' => 1,
                'virtual' => false,
                'lazy_loading' => 0,
                'created_at' => '2016-08-18 12:18:07',
                'updated_at' => '2016-08-18 12:18:07',
            ),
            11 =>
            array (
                'type' => 1,
                'name' => 'System Name',
                'related_module' => NULL,
                'relation_name' => NULL,
                'pivot_table' => NULL,
                'column_name' => 'system_name',
                'virtual_name' => NULL,
                'tooltip_text' => 'The invitation system status name.',
                'validation_rules' => 'alpha_dash|required|unique:invitation_statuses',
                'module_id' => 3,
                'order' => 1,
                'editable' => false,
                'disabled' => false,
                'hidden' => false,
                'is_system' => 1,
                'virtual' => false,
                'lazy_loading' => 0,
                'created_at' => '2016-08-18 12:18:07',
                'updated_at' => '2016-08-18 12:18:07',
            ),
        ));
    }
}
