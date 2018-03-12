<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SearchComparablesTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_comparables', function ($table) {
            $table->string('square_footage_price', 300)->nullable()->after('square_footage');
            $table->string('sale_date', 300)->nullable()->after('sale_price');
            $table->integer('year_built')->nullable()->after('sale_date');
            $table->integer('year_renovated')->nullable()->after('year_built');
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
            $table->dropColumn('square_footage_price');
            $table->dropColumn('sale_date');
            $table->dropColumn('year_built');
            $table->dropColumn('year_renovated');
        });
    }
}
