<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * We change the 'actie' column from an ENUM to a VARCHAR string.
     * This allows us to log any action strings (like bewijs_geüpload or gebruikers_gewijzigd)
     * without running into database truncation or restriction errors.
     */
    public function up(): void
    {
        Schema::table('activiteit', function (Blueprint $table) {
            // Modify column to be a string
            $table->string('actie', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activiteit', function (Blueprint $table) {
            // Revert back to the original ENUM constraint if rolled back
            $table->enum('actie', [
                "lid_aangemaakt",
                "lid_bijgewerkt",
                "lid_gewijzigd",
                "lid_verwijderd",
                "betaling_geregistreerd",
                "betaling_bijgewerkt",
                "betaling_goedgekeurd",
                "betaling_afgewezen",
                "bon_aangemaakt",
                "bon_gedownload",
                "wachtwoord_gewijzigd",
                "ingelogd",
                "uitgelogd"
            ])->change();
        });
    }
};
