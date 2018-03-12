<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserSearchesTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_searches', function ($table) {
            $table->float('phase1_paid_amount', 11, 2)->nullable()->after('token');
            $table->float('phase2_paid_amount', 11, 2)->nullable()->after('phase2_token');
            $table->integer('status')->nullable()->after('active_page');
            $table->decimal('appeal_amount', 11, 2)->nullable()->after('status');
            $table->integer('real_tax_amount')->nullable()->after('appeal_amount');
            $table->integer('case_1')->nullable()->after('real_tax_amount');
            $table->integer('apply_case_1')->nullable()->after('case_1');
            $table->longText('no_appeal_message', 500)->nullable()->after('apply_case_1');
            $table->integer('no_appeal_recommendation')->nullable()->after('no_appeal_message');
            $table->decimal('total_assessed_value_amount', 11, 2)->nullable()->after('no_appeal_recommendation');
            $table->integer('appeal_year')->nullable()->after('total_assessed_value_amount');
            $table->dateTime('sale_date')->nullable()->after('appeal_year');
            $table->float('sale_price', 11, 2)->nullable()->after('sale_date');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_searches', function ($table) {
            $table->dropColumn('phase1_paid_amount');
            $table->dropColumn('phase2_paid_amount');
            $table->dropColumn('status');
            $table->dropColumn('appeal_amount');
            $table->dropColumn('real_tax_amount');
            $table->dropColumn('case_1');
            $table->dropColumn('apply_case_1');
            $table->dropColumn('no_appeal_message');
            $table->dropColumn('no_appeal_recommendation');
            $table->dropColumn('total_assessed_value_amount');
            $table->dropColumn('appeal_year');
            $table->dropColumn('sale_date');
            $table->dropColumn('sale_price');
        });
    }
}
