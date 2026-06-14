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

        Schema::create('notificatie', function (Blueprint $table) {
            $table->increments('notificatie_id');
            $table->unsignedInteger('gebruiker_id');
            $table->foreign('gebruiker_id')->references('gebruiker_id')->on('gebruikers');
            $table->unsignedInteger('lid_id')->index();
            $table->foreign('lid_id')->references('lid_id')->on('leden');
            $table->enum('Notif_type', ["betaling_herinnering","betaling_goedgekeurd","Betaling_ingediend"]);
            $table->string('titel', 150);
            $table->tinyInteger('gelezen')->default(0);
            $table->timestamp('gestuurd_op');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificatie');
    }
};
