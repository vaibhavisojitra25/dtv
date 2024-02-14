<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCodeAutoRenewDeviceCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_code', function (Blueprint $table) {
            $table->tinyInteger('is_code_auto_renew')->default('0')->comment('1 for yes, 0 for no')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_code', function (Blueprint $table) {
            $table->dropColumn('is_code_auto_renew');
        });
    }
}
