<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('proxy')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->bigInteger('followers_count')->default(0)->unsigned();
            $table->bigInteger('following_count')->default(0)->unsigned();
            $table->bigInteger('posts_count')->default(0)->unsigned();
            $table->dateTime('followers_sync_at')->nullable();
            $table->dateTime('following_sync_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
