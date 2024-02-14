<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnToUserSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->after('last_billing_date', function ($table) {
                $table->string('billing_period')->nullable();
                $table->Integer('billing_period_num')->nullable();
                $table->string('billing_cycle')->nullable();
                $table->Integer('billing_cycle_num')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn('billing_period');
            $table->dropColumn('billing_period_num');
            $table->dropColumn('billing_cycle');
            $table->dropColumn('billing_cycle_num');
        });
    }
}
