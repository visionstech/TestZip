<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SearchComparablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_comparables', function ($table) {
            $table->integer('sale_price')->nullable()->after('subject_comps_detail_id');
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
            $table->dropColumn('sale_price');
        });
    }
}
