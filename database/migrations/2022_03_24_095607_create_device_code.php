<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_code', function (Blueprint $table) {
            $table->id();
            $table->string('user_id',8);
            $table->string('device_id');
            $table->string('code');
            $table->string('duration')->nullable();
            $table->dateTime('expire_date')->nullable();
            $table->tinyInteger('status')->default('1')->comment('0 for expire, 1 for active');
            $table->tinyInteger('is_verified')->default('1')->comment('0 for not verified, 1 for verified');
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
        Schema::dropIfExists('device_code');
    }
}
