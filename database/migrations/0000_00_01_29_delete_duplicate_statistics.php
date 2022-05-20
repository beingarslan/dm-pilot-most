<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeleteDuplicateStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DELETE s1
        FROM
            `statistics` s1
        INNER JOIN `statistics` s2
        WHERE
            `s1`.`id` < `s2`.`id`
        AND `s1`.`user_id` = `s2`.`user_id`
        AND `s1`.`account_id` = `s2`.`account_id`
        AND `s1`.`type` = `s2`.`type`
        AND `s1`.`sync_at` = `s2`.`sync_at`");

        Schema::table('statistics', function (Blueprint $table) {
            $table->index([
                'account_id',
                'user_id',
                'type',
                'sync_at',
            ]);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropIndex([
                'account_id',
                'user_id',
                'type',
                'sync_at',
            ]);
        });
    }
}
