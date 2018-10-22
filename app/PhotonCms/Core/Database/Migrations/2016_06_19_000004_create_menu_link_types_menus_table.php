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
            $table->integer('menu_id')->unsigned()->nullable()->index();
            $table->integer('menu_link_type_id')->unsigned()->nullable()->index();

            $table->foreign('menu_link_type_id')->references('id')->on('menu_link_types')->onDelete('set null');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('set null');
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
