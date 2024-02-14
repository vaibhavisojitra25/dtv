<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id',8);
            $table->tinyInteger('user_type')->default('0')->comment('1 for admin, 2 for customer');
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('phone_no',15)->nullable();
            $table->dateTime('trial_expire')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->tinyInteger('status')->default('1')->comment('0 for inactive, 1 for active');
            $table->tinyInteger('is_verified')->default('0')->comment('1 for verified, 0 for not');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
