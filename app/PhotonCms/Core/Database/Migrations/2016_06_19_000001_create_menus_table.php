<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->unique();
            $table->string('title', 255)->nullable()->default(null);
            $table->integer('max_depth')->unsigned()->nullable()->default(null);
            $table->integer('min_root')->unsigned()->nullable()->default(null);
            $table->boolean('is_system')->default(0);
            $table->string('description')->nullable()->default(null);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('menus');
    }
}