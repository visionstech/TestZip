<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountiesTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counties', function (Blueprint $table) {
            $table->integer('county_id')->primary();
            $table->string('county_name');
            $table->integer('state_id');
			$table->dateTime('date_of_value')->nullable();
			$table->dateTime('notice_date')->nullable();
			$table->dateTime('appeal_deadline_date')->nullable();
			$table->string('county_link')->nullable();
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
        Schema::drop('counties');
    }
}
