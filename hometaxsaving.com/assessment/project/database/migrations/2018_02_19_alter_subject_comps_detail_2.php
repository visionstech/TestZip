<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubjectCompsDetail2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subject_comps_details', function ($table) {
                $table->integer('year_built')->after('fireplace');
                $table->string('air_conditioning')->after('year_built');
                $table->string('owner_name')->after('air_conditioning');
                $table->longText('corelogic_response')->after('owner_name');
                $table->string('basement_area ')->after('corelogic_response');
                $table->string('parcel_size')->after('basement_area');
                $table->string('exterior')->after('parcel_size');
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
                $table->dropColumn('year_built');
                $table->dropColumn('air_conditioning');
                $table->dropColumn('owner_name');
                $table->dropColumn('corelogic_response');
                $table->dropColumn('basement_area ');
                $table->dropColumn('parcel_size');
                $table->dropColumn('exterior');
        });
    }
}
