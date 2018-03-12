<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectCompsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_comps_details', function (Blueprint $table) {
            $table->integer('subject_comps_id')->primary();
            $table->integer('system_object_type_id');
            $table->integer('ref_object_id');
            $table->string('type_of_house', 255)->nullable();
            $table->string('square_footage', 100)->nullable();
            $table->string('bedrooms', 100)->nullable();
            $table->string('bathrooms', 100)->nullable();
            $table->string('unfinished_space', 100)->nullable();
            $table->string('finished_space', 100)->nullable();
            $table->string('garage', 100)->nullable();
            $table->string('carport', 100)->nullable();
            $table->string('porch_deck', 100)->nullable();
            $table->string('patio', 100)->nullable();
            $table->string('swimming_pool', 100)->nullable();
            $table->string('fireplace', 100)->nullable();
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
        Schema::drop('subject_comps_details');
    }
}
