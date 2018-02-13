<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestForNodes extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('lft');
            $table->integer('rgt');
            $table->integer('parent_id')->nullable()->default(null);
            $table->integer('depth')->default(0);
            $table->integer('scope_id')->nullable()->default(null);
            $table->string('anchor_text', 255)->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('artists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('lft');
            $table->integer('rgt');
            $table->integer('parent_id')->nullable()->default(null);
            $table->integer('depth')->default(0);
            $table->integer('scope_id')->nullable()->default(null);
            $table->string('anchor_text', 255)->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('albums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('lft');
            $table->integer('rgt');
            $table->integer('parent_id')->nullable()->default(null);
            $table->integer('depth')->default(0);
            $table->integer('scope_id')->nullable()->default(null);
            $table->string('anchor_text', 255)->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('lft');
            $table->integer('rgt');
            $table->integer('parent_id')->nullable()->default(null);
            $table->integer('depth')->default(0);
            $table->integer('scope_id')->nullable()->default(null);
            $table->string('anchor_text', 255)->nullable()->default(null);
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
        Schema::drop('genres');
        Schema::drop('artists');
        Schema::drop('albums');
        Schema::drop('songs');
    }
}