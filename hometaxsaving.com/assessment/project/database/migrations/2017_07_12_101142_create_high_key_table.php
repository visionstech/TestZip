<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHighKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('pf_high_key', function (Blueprint $table) {
			$table->string('key_description', 1000);
			$table->string('schema_name', 500);
			$table->string('table_name', 500);
			$table->string('column_name', 500);
			$table->bigInteger('high_key');
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
		Schema::drop('pf_high_key');
    }
}
