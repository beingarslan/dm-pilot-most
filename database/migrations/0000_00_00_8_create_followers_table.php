<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->enum('type', [1, 2])->default(1);
            $table->string('username', 50);
            $table->bigInteger('pk');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts');
            $table->unique(['account_id', 'type', 'pk']);
            $table->index(['account_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followers');
    }
}
