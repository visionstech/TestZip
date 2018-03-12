<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLookupTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // PF_LOOKUP_TYPES table
		Schema::create('pf_lookup_types', function (Blueprint $table) {
            $table->integer('lookup_type_id')->primary();
			$table->string('name', 200);
			$table->string('description', 1000)->nullable();
			$table->integer('created_by');
			$table->dateTime('created_at');
			$table->integer('updated_by');
			$table->dateTime('updated_at');
            $table->dateTime('end_date')->nullable();;
			
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
