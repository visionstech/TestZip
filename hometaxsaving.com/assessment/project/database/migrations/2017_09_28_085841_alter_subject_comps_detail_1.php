<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubjectCompsDetail1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subject_comps_details', function ($table) {
                $table->integer('comparable_number')->default(0)->after('ref_object_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subject_comps_details', function ($table) {
                $table->dropColumn('comparable_number');
        });
    }
}
