<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIsUsedToCreditHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_history', function (Blueprint $table) {
            $table->renameColumn('is_used', 'is_credited');
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
            $table->renameColumn('is_credited', 'is_used');

        });
    }
}
