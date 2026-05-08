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
            $table->char('bon_id', 36)->primary();
            $table->char('betaling_id', 36);
            $table->foreign('betaling_id')->references('betaling_id')->on('betalingen');
            $table->string('bon_nummer', 30);
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
