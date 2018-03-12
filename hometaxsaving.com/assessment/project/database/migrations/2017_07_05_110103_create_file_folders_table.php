<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // PF_FILE_FOLDERS table
		Schema::create('pf_file_folders', function (Blueprint $table) {
            $table->integer('file_folder_id')->primary();
			$table->string('name', 200);
			$table->string('description', 1000);
			$table->integer('parent_folder_id');
			$table->integer('sort_order');
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
