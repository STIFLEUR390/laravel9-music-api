<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genres_music', function (Blueprint $table) {
            $table->id();
            $table->foreignId('genre_id')->constrained('genres')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('music_id')->nullable()->constrained('music')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genres_music');
    }
};
