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
            $table->increments('betaling_id');
            $table->unsignedInteger('lid_id')->index();
            $table->foreign('lid_id')->references('lid_id')->on('leden');
            $table->decimal('bedrag', 10, 2);
            $table->enum('methode', ["fysiek","overmaking"]);
            $table->enum('status', ["Openstaand","betaald","niet_betaald"]);
            $table->tinyInteger('maand');
            $table->year('jaar');
            $table->string('betaling_bewijs', 255)->nullable();
            $table->date('ingediend_op')->nullable();
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
