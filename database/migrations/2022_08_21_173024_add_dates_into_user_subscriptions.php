<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesIntoUserSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('starts_at')->nullable()->after('plan_code');
            $table->string('activation_date')->nullable()->after('starts_at');
            $table->string('expiry_date')->nullable()->after('activation_date');
            $table->string('trial_days')->nullable()->after('expiry_date');
            $table->string('trial_expiry_date')->nullable()->after('trial_days');
            $table->string('next_billing_date')->nullable()->after('trial_expiry_date');
            $table->string('last_billing_date')->nullable()->after('next_billing_date');
            $table->string('canceled_date')->nullable()->after('last_billing_date');

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
            $table->dropColumn('starts_at');
            $table->dropColumn('activation_date');
            $table->dropColumn('expiry_date');
            $table->dropColumn('trial_days');
            $table->dropColumn('trial_expiry_date');
            $table->dropColumn('next_billing_date');
            $table->dropColumn('last_billing_date');
            $table->dropColumn('canceled_date');
        });
    }
}
