<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRssItemUrlColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rss_items', function (Blueprint $table) {
            $table->dropUnique(['rss_id', 'url']);
        });

        Schema::table('rss_items', function (Blueprint $table) {
            $table->text('url')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rss_items', function (Blueprint $table) {
            $table->string('url')->change();
            $table->unique(['rss_id', 'url']);
        });
    }
}
