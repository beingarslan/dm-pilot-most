<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');

            /**
             * IG
             *
             * pk
             * code
             * thumb_url
             * location_id
             * location
             */

            $table->integer('account_id');
            $table->enum('type', ['post', 'album', 'story'])->default('post');
            $table->longText('ig')->nullable();
            $table->text('caption')->nullable();
            $table->enum('status', ['1', '2', '3'])->default('1');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('posted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
