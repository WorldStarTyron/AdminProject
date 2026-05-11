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
    $table->char('lid_id', 36)->index();
    $table->foreign('lid_id')->references('lid_id')->on('leden')->onDelete('cascade');
    $table->decimal('bedrag', 10, 2);
    $table->enum('methode', ['Geld', 'Overmaking']);
    $table->enum('status', ['Betaald', 'Niet Betaald', 'Afwachting'])->default('Niet Betaald');
    $table->date('betalingsdatum')->nullable();
    $table->tinyInteger('maand');
    $table->year('jaar');
    $table->string('bewijs_bestand', 255)->nullable();
    $table->timestamp('ingediend_op')->useCurrent();
    $table->timestamp('bijgewerkt_op')->useCurrent()->useCurrentOnUpdate();
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
