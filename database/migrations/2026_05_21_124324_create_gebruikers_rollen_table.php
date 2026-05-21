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

        Schema::create('gebruikers_rollen', function (Blueprint $table) {
            $table->unsignedInteger('gebruiker_id');
            $table->foreign('gebruiker_id')->references('gebruiker_id')->on('gebruikers');
            $table->unsignedTinyInteger('rol_id');
            $table->foreign('rol_id')->references('rol_id')->on('rollen');
            $table->timestamp('toegewezen_op')->useCurrent();
            $table->primary(['gebruiker_id', 'rol_id']);
        });

        Schema::enableForeignKeyConstraints();
    }
 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gebruikers_rollen');
    }
};
