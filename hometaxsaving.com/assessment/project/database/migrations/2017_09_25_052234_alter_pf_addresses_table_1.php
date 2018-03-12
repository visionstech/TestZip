<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPfAddressesTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pf_addresses', function ($table) {
                $table->integer('receive_notification')->default(0)->after('mobile_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pf_addresses', function ($table) {
                $table->dropColumn('receive_notification');
        });
    }
}
