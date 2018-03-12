<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // PF_LOOKUPS table
		Schema::create('pf_lookups', function (Blueprint $table) {
            $table->integer('lookup_id')->primary();
			$table->integer('lookup_type_id');
			$table->string('name', 200);
			$table->string('description', 1000)->nullable();
			$table->string('value', 400);
                        $table->string('value1', 400)->nullable();
                        $table->string('value2', 400)->nullable();
			$table->integer('parent_lookup_id')->nullable();
			$table->integer('display_order')->nullable();
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
        Schema::drop('pf_lookups');
    }
}
