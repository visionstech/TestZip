<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // PF_FILES table
		Schema::create('md_in_out_cycle', function (Blueprint $table) {
            $table->integer('cycle_id')->primary();
			$table->integer('county_id');
			$table->date('incycle_notice_date');
			$table->date('incycle_deadline_date');
            $table->string('incycle_link');
            $table->date('outcycle_notice_date');
            $table->date('outcycle_deadline_date');
            $table->string('outcycle_link');
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
        //
    }
}
