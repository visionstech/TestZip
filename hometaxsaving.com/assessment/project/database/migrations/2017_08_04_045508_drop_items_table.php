<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('items');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('item_id');
            $table->integer('item_name');
            $table->string('item_value');
            $table->integer('created_by');
            $table->dateTime('created_at');
            $table->integer('updated_by');
            $table->dateTime('updated_at');
            $table->dateTime('end_date')->nullable();			
        });
    }
}
