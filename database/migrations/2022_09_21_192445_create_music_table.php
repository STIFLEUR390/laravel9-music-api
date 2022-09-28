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
        Schema::disableForeignKeyConstraints();
        Schema::create('music', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('artist_id')->nullable()->constrained('artists')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreignId('album_id')->nullable()->constrained('albums')->onUpdate('cascade')->onDelete('cascade');
            $table->string('artist_id')->nullable();
            $table->string('album_id')->nullable();
            $table->string('title')->nullable()->comment("Obtenir le titre du morceau");
            $table->string('playtime')->nullable()->comment("Durée de lecture totale des pistes");
            $table->string('playtime_s')->nullable()->comment("Temps de jeu total en quelques secondes");
            $table->longText('artwork')->nullable()->comment("Artwork du morceau");
            $table->string('composers')->nullable()->comment("Les compositeurs du morceau");
            $table->string('track_number')->nullable()->comment("numéro de piste sur le nombre total de l'album, par exemple. 1/12");
            $table->string('copyright')->nullable()->comment("Informations de copyright de la piste");
            $table->string('format')->nullable()->comment("Format de fichier du fichier, par exemple. mp4");
            $table->text('image')->nullable();
            $table->text('path');
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
        Schema::dropIfExists('music');
    }
};
