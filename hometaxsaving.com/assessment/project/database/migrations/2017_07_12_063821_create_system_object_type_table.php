<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemObjectTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('pf_system_object_types', function (Blueprint $table) {
			$table->integer('system_object_type_id')->primary();
			$table->string('name', 300);
			$table->string('description', 1000);
			$table->string('table_name', 500);
			$table->string('column_name', 500);
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
		Schema::drop('pf_system_object_types');

    }
}
