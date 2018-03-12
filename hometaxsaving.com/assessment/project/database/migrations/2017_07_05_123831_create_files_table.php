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
		Schema::create('pf_files', function (Blueprint $table) {
            $table->integer('file_id')->primary();
			$table->integer('system_object_type_id');
			$table->integer('file_folder_id');
			$table->integer('ref_object_id');
			$table->string('file_name', 200);
			$table->string('description', 1000);
			$table->string('mime_type', 100);
			$table->string('file_size', 50);
			$table->binary('file_blob');
			$table->integer('status');
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
