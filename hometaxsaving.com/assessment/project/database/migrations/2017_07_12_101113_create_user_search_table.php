<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_searches', function (Blueprint $table) {
			$table->integer('user_search_id')->primary();
			$table->integer('system_user_id');
			$table->integer('address_id');
			$table->dateTime('search_date');
			$table->decimal('latest_assessment_value',10,2);
			$table->binary('comparables');
                        $table->string('token', 255);
			$table->integer('active_page');
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
        //
		Schema::drop('user_searches');
    }
}
