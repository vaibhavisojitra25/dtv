<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnIntoDeviceCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_code', function (Blueprint $table) {
            $table->dropColumn('duration');
            $table->dropColumn('expire_date');
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
            $table->string('duration')->nullable();
            $table->dateTime('expire_date')->nullable();
        });
    }
}
