<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add user_type column to distinguish between admin and customers to the site
		Schema::table('users', function ($table) {
			$table->enum('user_type', ['0','1'])->after('ip_address')->default('0');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
			$table->dropColumn('user_type');
		});
    }
}
