<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatesTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->integer('state_id')->primary();
            $table->string('state_name');
			$table->string('state_abbr');
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
        Schema::drop('states');
    }
}
