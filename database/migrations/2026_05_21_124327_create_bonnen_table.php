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

        Schema::create('bonnen', function (Blueprint $table) {
            $table->increments('bon_id');
            $table->unsignedInteger('betaling_id')->unique();
            $table->string('bon_nummer', 30)->unique();
            $table->string('beschrijving', 255);
            $table->timestamp('aangemaakt_op')->useCurrent();
            $table->timestamp('gedownload_op')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonnen');
    }
};
