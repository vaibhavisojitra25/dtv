<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsIntoDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->tinyInteger('mac_type')->default('0')->after('mac_address')->comment('1 for Wifi, 2 for Eth');
            $table->string('mac_id')->nullable()->after('mac_type');
            $table->string('mac_key')->nullable()->after('mac_id');
            $table->string('platform')->nullable()->after('mac_key');
            $table->string('app_name')->nullable()->after('platform');
            $table->tinyInteger('is_cloud_sync')->default('0')->after('app_name')->comment('0 for Off, 1 for On');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('mac_type');
            $table->dropColumn('mac_id');
            $table->dropColumn('mac_key');
            $table->dropColumn('platform');
            $table->dropColumn('app_name');
            $table->dropColumn('is_cloud_sync');
        });
    }
}
