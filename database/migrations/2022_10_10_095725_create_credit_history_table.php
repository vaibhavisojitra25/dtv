<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_history', function (Blueprint $table) {
            $table->id();
            $table->Integer('credits')->default('0');
            $table->string('user_id',8);
            $table->string('device_id')->nullable();
            $table->text('plan_id');
            $table->tinyInteger('is_used')->default('1')->comment('0 for not used, 1 for used');
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
        Schema::dropIfExists('credit_history');
    }
}
