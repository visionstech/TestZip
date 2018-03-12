<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserSearchesTable5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Added field for phase2 token
		Schema::table('user_searches', function ($table) {
                $table->string('phase2_token',255)->after('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
		Schema::table('user_searches', function ($table) {
                $table->dropColumn('phase2_token');
        });
    }
}
