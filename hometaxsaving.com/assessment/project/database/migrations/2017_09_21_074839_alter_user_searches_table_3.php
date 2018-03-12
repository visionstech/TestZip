<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserSearchesTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_searches', function ($table) {
                $table->dropColumn('latest_assessment_value');
                $table->decimal('land_assessment_value',50,2)->after('last_name');
                $table->decimal('improvement_assessment_value',50,2)->after('land_assessment_value');
                $table->decimal('total_assessment_value',50,2)->after('improvement_assessment_value');
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
                $table->decimal('latest_assessment_value',50,2)->after('last_name');
                $table->dropColumn('land_assessment_value');
                $table->dropColumn('improvement_assessment_value');
                $table->dropColumn('total_assessment_value');
        });
    }
}
