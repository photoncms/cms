<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->unsigned();
            $table->bigInteger('category')->nullable()->default(null);
            $table->string('type', 255);
            $table->string('name', 255);
            $table->string('model_name', 255);
            $table->string('table_name', 255)->unique();
            $table->integer('max_depth')->unsigned()->nullable()->default(null);
            if (config('photon.use_slugs')) {
                $table->string('slug', 255)->nullable()->default(null);
            }
            $table->string('anchor_text', 2000)->nullable()->default(null);
            $table->text('anchor_html')->nullable()->default(null);
            $table->string('icon', 255)->nullable()->default(null);
            $table->boolean('reporting')->default(0);
            $table->boolean('lazy_loading')->default(0);
            $table->boolean('is_system')->default(0);
            $table->timestamps();
            $table->index('anchor_text');
        });
        Schema::create('model_meta_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('system_name', 255);
            $table->string('title', 255);
        });
        Schema::create('model_meta_data', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('module_id')->unsigned()->index();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->integer('model_meta_type_id')->unsigned()->index();
            $table->foreign('model_meta_type_id')->references('id')->on('model_meta_types')->onDelete('cascade');
            $table->string('value', 255);
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
        Schema::drop('model_meta_data');
        Schema::drop('model_meta_types');
        Schema::drop('modules');
    }
}