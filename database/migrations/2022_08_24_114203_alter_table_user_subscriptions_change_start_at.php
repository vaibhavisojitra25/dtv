<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUserSubscriptionsChangeStartAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dateTime('starts_at')->change();
            $table->dateTime('activation_date')->change();
            $table->dateTime('expiry_date')->change();
            $table->dateTime('trial_expiry_date')->change();
            $table->dateTime('next_billing_date')->change();
            $table->dateTime('last_billing_date')->change();
            $table->dateTime('canceled_date')->change();
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
            //
        });
    }
}
