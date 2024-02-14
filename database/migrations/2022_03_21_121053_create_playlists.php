<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaylists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->string('user_id',8);
            $table->string('type')->default('1')->comment('1 for xstrem, 2 for m3u');
            $table->string('dns')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('m3u_url')->nullable();
            $table->string('epg')->nullable();
            $table->string('user_agent')->nullable();
            $table->tinyInteger('status')->default('1')->comment('0 for expire, 1 for active');
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
        Schema::dropIfExists('playlists');
    }
}
