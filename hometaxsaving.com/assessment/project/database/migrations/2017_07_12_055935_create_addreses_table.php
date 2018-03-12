<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddresesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table pf_addresses
    
        Schema::create('pf_addresses', function (Blueprint $table) {
                    $table->integer('address_id')->primary();
                    $table->integer('system_object_type_id');
                    $table->integer('ref_object_id');
                    $table->integer('address_type');
                    $table->string('mobile_number', 30);
                    $table->string('address_line_1', 300);
                    $table->string('address_line_2', 300);
                    $table->string('address_line_3', 300);
                    $table->string('city', 100);
                    $table->string('postal_code', 60);
                    $table->string('state', 60);
                    $table->integer('country');
                    $table->string('province', 60);
                    $table->string('county', 60);
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
		Schema::drop('pf_addresses');
    }
}
