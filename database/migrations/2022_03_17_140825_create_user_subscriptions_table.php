<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 8);
            $table->text('customer_id');
            $table->text('product_id');
            $table->text('plan_id');
            $table->text('plan_code')->nullable();
            $table->Integer('device_limit')->defualt(0);
            $table->Integer('remaining_device_limit')->defualt(0);
            $table->Integer('trial_device_limit')->defualt(0);
            $table->Integer('trial_remaining_device_limit')->defualt(0);
            $table->text('subscription_id')->nullable();
            $table->tinyInteger('is_unlimited')->default('0');
            $table->tinyInteger('status')->default('1')->comment('0 expire, 1 live, 2 upcoming, 3 for cancelled');
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscriptions');
    }
}