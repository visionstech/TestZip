<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserSearchesTable4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_searches` CHANGE `comparables` `comparables` LONGBLOB NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `user_searches` CHANGE `comparables` `comparables` BLOB NOT NULL");
    }
}
