<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('system_name')->nullable();
            $table->string('anchor_text', 255)->nullable();
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
        Schema::drop('file_tags');
    }
}
