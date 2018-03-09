<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuLinkTypesMenusTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_link_types_menus', function (Blueprint $table) {
            $table->integer('menu_id')->unsigned()->index();
            $table->integer('menu_link_type_id')->unsigned()->index();

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
        Schema::drop('menu_link_types_menus');
    }
}