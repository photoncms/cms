<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFcmTokensTable extends Migration
{

    public function up()
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->string('token')->unique()->index();
            $table->increments('id');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('user')->unsigned()->nullable();   
        });
    }

    public function down()
    {
        Schema::drop('fcm_tokens');
    }
}