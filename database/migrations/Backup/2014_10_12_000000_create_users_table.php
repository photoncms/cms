<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_parent')->nullable()->default(null);
            $table->string('email', 255)->unique();
            $table->string('password', 60);
            $table->boolean('confirmed')->default(false);
            $table->string('confirmation_code', 255)->nullable();
            $table->rememberToken();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('anchor_text', 255)->nullable()->default(null);
            $table->integer('profile_image')->nullable()->default(null);
            $table->datetime('password_created_at')->nullable();
            $table->softDeletes();
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
        Schema::drop('users');
    }
}