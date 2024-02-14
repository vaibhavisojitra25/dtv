<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_id',8);
            $table->string('plan_name',50);
            $table->Integer('duration');
            $table->string('term',50)->nullable();
            $table->float('amount',10,2);
            $table->text('short_description');
            $table->text('description');
            $table->Integer('code_limit');
            $table->tinyInteger('is_free')->default(0);
            $table->string('expire_hour')->nullable();
            $table->tinyInteger('status')->default('1')->comment('0 for inactive, 1 for active');
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
        Schema::dropIfExists('plans');
    }
}
