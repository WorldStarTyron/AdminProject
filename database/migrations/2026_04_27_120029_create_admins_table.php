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

        Schema::create('admins', function (Blueprint $table) {
            $table->char('admin_id', 36)->primary();
            $table->char('gebruiker_id', 36);
            $table->foreign('gebruiker_id')->references('gebruiker_id')->on('gebruikers');
            $table->char('aangemaakt_door', 36);
            $table->foreign('aangemaakt_door')->references('gebruiker_id')->on('gebruikers');
            $table->timestamp('aangemaakt_op')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
