<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('activiteit', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedInteger('gebruiker_id')->index();
            $table->foreign('gebruiker_id')->references('gebruiker_id')->on('gebruikers');
            $table->enum('actie', ["lid_aangemaakt","lid_bijgewerkt","lid_gewijzigd","lid_verwijderd","betaling_geregistreerd","betaling_goedgekeurd","betaling_afgewezen","bon_aangemaakt","bon_gedownload","wachtwoord_gewijzigd","ingelogd","uitgelogd"]);
            $table->json('details')->nullable();
            $table->timestamp('aangemaakt_op')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activiteit');
    }
};
