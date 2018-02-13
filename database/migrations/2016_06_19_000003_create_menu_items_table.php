<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lft');
            $table->integer('rgt');
            $table->integer('parent_id')->nullable()->default(null);
            $table->integer('depth')->default(0);
            $table->integer('menu_id')->unsigned()->index();
            $table->integer('menu_link_type_id')->unsigned()->index();
            $table->string('title', 255)->nullable()->default(null);
            $table->text('resource_data')->nullable()->default(null);
            $table->text('entry_data')->nullable()->default(null);
            $table->string('icon', 255)->nullable()->default(null);
            $table->string('slug', 255)->nullable()->default(null);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            $table->foreign('menu_link_type_id')->references('id')->on('menu_link_types')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('menu_items');
    }
}