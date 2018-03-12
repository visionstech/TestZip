<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
			$table->string('ip_address', 50)->after('remember_token')->nullable();
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
		Schema::table('users', function ($table) {
			$table->dropColumn('ip_address');
			$table->dropColumn('end_date');
		});
    }
}
