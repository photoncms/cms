<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuLinkTypesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_link_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('title', 255);
            $table->boolean('clickable')->default(true);
            $table->boolean('is_system')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('menu_link_types');
    }
}