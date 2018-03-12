<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCountiesTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counties', function ($table) {
                    $table->string('contact_person', 300)->nullable()->after('county_link');
                    $table->string('address_line_1', 300)->nullable()->after('contact_person');
                    $table->string('address_line_2', 300)->nullable()->after('address_line_1');
                    $table->string('address_line_3', 300)->nullable()->after('address_line_2');
                    $table->string('city', 100)->nullable()->after('address_line_3');
                    $table->string('postal_code', 60)->nullable()->after('city');
                    $table->string('province', 60)->nullable()->after('postal_code');
                    $table->string('fax', 60)->nullable()->after('province');
                    $table->string('email', 60)->nullable()->after('fax');
                    $table->string('working_hours', 60)->nullable()->after('email');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counties', function ($table) {
                $table->dropColumn('contact_person');
                $table->dropColumn('address_line_1');
                $table->dropColumn('address_line_2');
                $table->dropColumn('address_line_3');
                $table->dropColumn('city');
                $table->dropColumn('postal_code');
                $table->dropColumn('province');
                $table->dropColumn('fax');
                $table->dropColumn('email');
                $table->dropColumn('working_hours');   
        });
    }
}
