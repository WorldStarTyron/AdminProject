<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Voegt de kolom 'volgende_deadline' toe aan de betalingen tabel.
     * Vult bestaande betaalde betalingen automatisch met een deadline
     * (ingediend_op + 1 maand).
     */
    public function up(): void
    {
        Schema::table('betalingen', function (Blueprint $table) {
            $table->date('volgende_deadline')->nullable()->after('ingediend_op');
        });

        // Data-fix: vul volgende_deadline voor bestaande betaalde betalingen
        $betalingen = DB::table('betalingen')
            ->whereIn('status', ['betaald', 'goed_gekeurd'])
            ->whereNotNull('ingediend_op')
            ->whereNull('volgende_deadline')
            ->get();

        foreach ($betalingen as $betaling) {
            $volgendeDeadline = Carbon::parse($betaling->ingediend_op)->addMonthNoOverflow()->format('Y-m-d');

            DB::table('betalingen')
                ->where('betaling_id', $betaling->betaling_id)
                ->update(['volgende_deadline' => $volgendeDeadline]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('betalingen', function (Blueprint $table) {
            $table->dropColumn('volgende_deadline');
        });
    }
};
