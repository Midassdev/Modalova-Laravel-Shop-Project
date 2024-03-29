<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColorWhiteListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('color_white_list', function (Blueprint $table) {
            $table->bigInteger('white_list_id')->unsigned()->nullable();
            $table->bigInteger('color_id')->unsigned()->nullable();

            $table->primary(['white_list_id', 'color_id']);

            $table->foreign('white_list_id')->references('id')
                ->on('white_lists')->onDelete('cascade');

            $table->foreign('color_id')->references('id')
                ->on('colors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('color_white_list');
    }
}
