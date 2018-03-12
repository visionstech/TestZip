<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_searches', function ($table) {
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
        Schema::table('user_searches', function ($table) {
            $table->dropColumn('sale_date');
            $table->dropColumn('year_built');
            $table->dropColumn('year_renovated');
        });
    }
}
