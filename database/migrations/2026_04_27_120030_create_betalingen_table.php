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

        Schema::create('betalingen', function (Blueprint $table) {
            $table->char('betaling_id', 36)->primary();
            $table->char('lid_id', 36);
            $table->foreign('lid_id')->references('lid_id')->on('leden');
            $table->char('goedgekeurd_door', 36)->nullable();
            $table->foreign('goedgekeurd_door')->references('gebruiker_id')->on('gebruikers');
            $table->char('ontvangen_door', 36)->nullable();
            $table->foreign('ontvangen_door')->references('gebruiker_id')->on('gebruikers');
            $table->decimal('bedrag', 10, 2);
            $table->enum('methode', ["contant","overboeking","ideal","tikkie","overig"]);
            $table->enum('status', ["in_behandeling","goedgekeurd","afgewezen"]);
            $table->date('betalingsdatum')->nullable();
            $table->tinyInteger('maand');
            $table->year('jaar');
            $table->string('omschrijving', 255)->nullable();
            $table->string('bewijs_bestand', 255)->nullable();
            $table->text('notitie_admin')->nullable();
            $table->timestamp('ingediend_op')->useCurrent();
            $table->timestamp('bijgewerkt_op')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('betalingen');
    }
};
