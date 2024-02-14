<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddedByIntoUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->Integer('credits')->default('0')->after('phone_no');
            $table->tinyInteger('is_multi_dns')->default('0')->after('is_verified')->comment('0 for off, 1 for on');
            $table->string('added_by',8)->nullable()->after('is_multi_dns');
            $table->string('user_type')->comment('1 for admin, 2 for customer, 3 for reseller, 4 for subreseller')->change();
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
            $table->dropColumn('added_by');
        });
    }
}
