<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->string('name', 255);
            $table->bigInteger('related_module')->nullable()->default(null);
            $table->string('relation_name', 255)->nullable()->default(null);
            $table->string('pivot_table', 255)->nullable()->default(null);
            $table->string('column_name', 255)->nullable()->default(null);
            $table->string('virtual_name', 255)->nullable()->default(null);
            $table->string('tooltip_text')->nullable()->default(null);
            $table->string('validation_rules', 255)->nullable()->default(null);
            $table->bigInteger('module_id')->unsigned()->index();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('editable')->default(1);
            $table->boolean('disabled')->default(0);
            $table->boolean('hidden')->default(0);
            $table->boolean('is_system')->default(0);
            $table->boolean('virtual')->default(0);
            $table->boolean('lazy_loading')->default(0);
            $table->boolean('can_create_search_choice')->default(0);
            $table->boolean('is_default_search_choice')->default(0);
            $table->string('active_entry_filter', 255)->nullable()->default(null);
            $table->boolean('flatten_to_optgroups')->default(0);
            $table->string('default')->nullable()->default(null);
            $table->string('local_key')->nullable()->default(null);
            $table->string('foreign_key')->nullable()->default(null);
            $table->boolean('nullable')->default(false);
            $table->boolean('indexed')->default(false);
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
        Schema::drop('fields');
    }
}
