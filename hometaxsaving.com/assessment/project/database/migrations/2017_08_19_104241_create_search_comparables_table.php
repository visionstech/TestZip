<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchComparablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_comparables', function (Blueprint $table) {
            $table->integer('search_comparable_id')->primary();
            $table->integer('user_searches_id');
            $table->integer('subject_comps_detail_id');
            $table->string('distance_from_subject', 100)->nullable();
            $table->string('date_of_sale', 100)->nullable();
            $table->string('parcel_size', 100)->nullable();
            $table->string('total_bedrooms', 100)->nullable();
            $table->string('total_bathrooms', 100)->nullable();
            $table->string('finished_space', 100)->nullable();
            $table->string('unfinished_space', 100)->nullable();
            $table->string('garage', 100)->nullable();
            $table->string('carport', 100)->nullable();
            $table->string('swimming_pool', 100)->nullable();
            $table->string('fireplace', 100)->nullable();
            $table->string('total_adjustment_price', 100)->nullable();
            $table->string('price_after_adjustment', 100)->nullable();
            $table->integer('lookup_id')->nullable();
            $table->integer('lookup_value')->nullable();
            $table->integer('lookup_count')->nullable();
            $table->integer('created_by');
            $table->dateTime('created_at');
            $table->integer('updated_by');
            $table->dateTime('updated_at');
            $table->dateTime('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('search_comparables');
    }
}
