<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lists_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lists_id')->unsigned();
            $table->text('text');
            $table->dateTime('used_at')->nullable();

            $table->foreign('lists_id')->references('id')->on('lists');
            $table->index('lists_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lists_items');
    }
}
