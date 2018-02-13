<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsFileTagsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets_file_tags', function (Blueprint $table) {
            $table->integer('asset_id')->unsigned()->index();
            $table->integer('file_tag_id')->unsigned()->index();
            
            $table->primary(['asset_id', 'file_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assets_file_tags');
    }
}
