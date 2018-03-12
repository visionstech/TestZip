<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserSearchesTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_searches', function ($table) {
                $table->string('first_name', 50)->after('search_date');
                $table->string('last_name', 50)->after('first_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_searches', function ($table) {
                $table->dropColumn('first_name');
                $table->dropColumn('last_name');
        });
    }
}
