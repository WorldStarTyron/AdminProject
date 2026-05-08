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

        Schema::create('leden', function (Blueprint $table) {
            $table->char('lid_id', 36)->primary();
            $table->char('Naam', 100);
            $table->string('Email');
            $table->string('telefoonnummer', 20)->nullable();
            $table->string('adres', 255)->nullable();
            $table->string('woonplaats', 100)->nullable();
            $table->date('geboortedatum')->nullable();
            $table->enum('betaalstatus', ["Betaald","Niet Betaald"]);
            $table->timestamp('lid_sinds')->useCurrent();
            $table->timestamp('bijgewerkt_op')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leden');
    }
};
