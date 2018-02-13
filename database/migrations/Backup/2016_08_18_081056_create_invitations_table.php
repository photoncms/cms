<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            $table->string('anchor_text', 255)->nullable();
            $table->string('invitation_code', 255)->nullable();
            $table->integer('invitation_status')->nullable();
            $table->datetime('first_sent');
            $table->datetime('resent_at');
            $table->timestamps();
            $table->index('anchor_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('invitations');
    }
}
