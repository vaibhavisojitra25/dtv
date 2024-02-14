<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddedByIntoCreditHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_history', function (Blueprint $table) {
            $table->string('credited_to',8)->nullable()->after('is_credited');
            $table->string('added_by',8)->nullable()->after('credited_to');
            $table->text('plan_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_history', function (Blueprint $table) {
            $table->dropColumn('added_by');
        });
    }
}
