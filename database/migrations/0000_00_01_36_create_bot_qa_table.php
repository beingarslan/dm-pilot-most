<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotQATable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_qa', function (Blueprint $table) {
            $table->id();

            $table->integer('bot_id')->unsigned();
            $table->integer('ordering');
            $table->longText('hears');
            $table->tinyInteger('message_type')->unsigned();
            $table->longText('message');

            $table->index('bot_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_qa');
    }
}
