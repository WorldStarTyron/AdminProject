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
        Schema::create('gebruikers', function (Blueprint $table) {
            $table->char('gebruiker_id', 36)->primary();
            $table->string('naam', 100);
            $table->string('email', 150)->unique();
            $table->string('wachtwoord_hash', 255);
            $table->enum('rol', ["lid","admin","superadmin"]);
            $table->tinyInteger('actief')->default(1);
            $table->timestamp('aangemaakt_op')->useCurrent();
            $table->timestamp('bijgewerkt_op')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gebruikers');
    }
};
