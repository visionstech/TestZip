<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SearchComparablesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_comparables', function ($table) {
            $table->dropColumn('sale_date');
            $table->dateTime('date_of_sale')->change();
            $table->integer('sale_price_divided_sf')->nullable()->after('date_of_sale');
            $table->string('data_source', 300)->nullable()->after('sale_price_divided_sf');
            $table->integer('subsidy')->nullable()->after('data_source');
            $table->string('leasehold', 300)->nullable()->after('subsidy');
            $table->integer('square_footage')->nullable()->after('leasehold');
            $table->string('exterior', 300)->nullable()->after('square_footage');
            $table->integer('gross_living_area')->nullable()->after('exterior');
            $table->string('basement', 300)->nullable()->after('gross_living_area');
            $table->string('basement_type', 300)->nullable()->after('basement');
            $table->integer('net_adjustment')->nullable()->after('basement_type');
            $table->integer('land_assessment_value')->nullable()->after('lookup_count');
            $table->integer('total_assessment_value')->nullable()->after('land_assessment_value');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_comparables', function ($table) {
            $table->string('sale_date');
            $table->dateTime('date_of_sale ')->nullable();
            $table->dropColumn('sale_price_divided_sf');
            $table->dropColumn('data_source');
            $table->dropColumn('subsidy');
            $table->dropColumn('leasehold');
            $table->dropColumn('square_footage');
            $table->dropColumn('exterior');
            $table->dropColumn('gross_living_area');
            $table->dropColumn('basement');
            $table->dropColumn('basement_type');
            $table->dropColumn('net_adjustment');
            $table->dropColumn('land_assessment_value');
            $table->dropColumn('total_assessment_value');
        });
    }
}
