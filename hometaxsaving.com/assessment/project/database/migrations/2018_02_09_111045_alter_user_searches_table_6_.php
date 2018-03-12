<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserSearchesTable6 extends Migration
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
                $table->string('cycle_type',255)->after('appeal_year');
                $table->string('appeal_type',255)->after('cycle_type');
                $table->date('appeal_deadline_date')->after('appeal_type');
                $table->string('latest_assesement_year',255)->after('appeal_deadline_date');
                
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
