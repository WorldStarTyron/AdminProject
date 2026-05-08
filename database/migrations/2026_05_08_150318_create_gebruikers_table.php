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

        Schema::create('gebruikers', function (Blueprint $table) {
            $table->char('gebruiker_id', 36)->primary();
            $table->string('naam', 100);
            $table->string('email', 150)->unique();
            $table->string('wachtwoord_hash', 255);
            $table->unsignedTinyInteger('rol_id')->default(1);
            $table->foreign('rol_id')->references('rol_id')->on('rollen');
            $table->timestamp('aangemaakt_op')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gebruikers');
    }
};
