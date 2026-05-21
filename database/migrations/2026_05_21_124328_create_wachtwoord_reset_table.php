<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('wachtwoord_reset', function (Blueprint $table) {
            $table->increments('reset_id');
            $table->unsignedInteger('gebruiker_id')->index();
            $table->foreign('gebruiker_id')->references('gebruiker_id')->on('gebruikers');
            $table->string('token', 255);
            $table->timestamp('verlopen_op')->default(DB::raw('(CURRENT_TIMESTAMP + INTERVAL 1 HOUR)'));
            $table->tinyInteger('gebruikt');
            $table->timestamp('aangemaakt_op')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wachtwoord_reset');
    }
};
