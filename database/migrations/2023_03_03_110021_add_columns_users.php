<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_unlimited_credit')->default('0')->comment('1 for yes, 0 for no')->after('credits');
            $table->Integer('unlimited_credits')->default('0')->after('credits');
            $table->dateTime('credit_expire_date')->after('is_unlimited_credit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_unlimited_credit');
            $table->dropColumn('unlimited_credits');
            $table->dropColumn('credit_expire_date');
        });
    }
}
