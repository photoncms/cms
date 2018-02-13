<?php

use Illuminate\Database\Seeder;

class InitialValuesInvitationStatusesModuleSeeder extends Seeder
{
    public function run()
    {

        // Data
        \Schema::disableForeignKeyConstraints();
        \DB::table('invitation_statuses')->delete();
        \DB::table('invitation_statuses')->insert(array (
            0 =>
            array (
                'id' => 1,
                'title' => 'Pending',
                'system_name' => 'pending',
                'anchor_text' => 'Pending',
                'created_at' => '2016-08-18 12:19:03',
                'updated_at' => '2016-08-18 12:19:03',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            1 =>
            array (
                'id' => 2,
                'title' => 'Resent',
                'system_name' => 'resent',
                'anchor_text' => 'Resent',
                'created_at' => '2016-08-18 12:20:19',
                'updated_at' => '2016-08-18 12:20:19',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            2 =>
            array (
                'id' => 3,
                'title' => 'Canceled',
                'system_name' => 'canceled',
                'anchor_text' => 'Canceled',
                'created_at' => '2016-08-18 12:20:19',
                'updated_at' => '2016-08-18 12:20:19',
                'created_by' => 1,
                'updated_by' => 1,
            ),
            3 =>
            array (
                'id' => 4,
                'title' => 'Used',
                'system_name' => 'used',
                'anchor_text' => 'Used',
                'created_at' => '2016-08-18 12:20:29',
                'updated_at' => '2016-08-18 12:20:29',
                'created_by' => 1,
                'updated_by' => 1,
            ),
        ));
        \Schema::enableForeignKeyConstraints();
    }
}
