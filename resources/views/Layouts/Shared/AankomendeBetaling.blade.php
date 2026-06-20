<!-- Kaartje: Aankomende Betalingen -->
<div class="bg-white rounded-xl border border-gray-200/80 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.02)] flex flex-col justify-between">
    <div>

        <!-- Koptekst met titel en totaal aantal leden -->
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-base font-semibold text-slate-800">
                <i class="fa-solid fa-calendar-clock text-slate-400 mr-2"></i>
                Aankomende Betalingen
            </h3>
            <!-- Totaal aantal leden met een deadline -->
            <span class="text-xs text-gray-400 font-medium">
                {{ $deadlineLeden->total() }} leden
            </span>
        </div>

        <!-- Lijst van leden met aankomende deadlines -->
        <div class="space-y-3.5">
            @forelse($deadlineLeden as $lid)
                @php
                    // Gebruik de voorberekende deadline uit de controller
                    $deadline = $lid->_deadline;

                    if ($deadline) {
                        // Bereken hoeveel dagen er nog over zijn tot de deadline
                        $daysLeft = now()->startOfDay()->diffInDays($deadline->startOfDay(), false);

                        // Kies de tekst en kleur op basis van hoe dringend het is
                        if ($daysLeft < 0) {
                            // Deadline is al verlopen
                            $daysText = 'Verlopen (' . abs($daysLeft) . ' ' . (abs($daysLeft) == 1 ? 'dag' : 'dagen') . ')';
                            $colorClass = 'red';
                        } elseif ($daysLeft == 0) {
                            // Deadline is vandaag
                            $daysText = 'Vandaag';
                            $colorClass = 'red';
                        } elseif ($daysLeft == 1) {
                            // Deadline is morgen
                            $daysText = 'Morgen';
                            $colorClass = 'amber';
                        } elseif ($daysLeft <= 3) {
                            // Deadline is binnen 3 dagen
                            $daysText = 'Binnen ' . $daysLeft . ' dagen';
                            $colorClass = 'amber';
                        } elseif ($daysLeft <= 5) {
                            // Deadline is binnen 5 dagen
                            $daysText = 'Binnen ' . $daysLeft . ' dagen';
                            $colorClass = 'sky';
                        } else {
                            // Deadline is binnen 6 of 7 dagen
                            $daysText = 'Binnen ' . $daysLeft . ' dagen';
                            $colorClass = 'violet';
                        }

                        $formattedDeadline = $deadline->format('d-m-Y');
                    } else {
                        // Geen deadline gevonden voor dit lid
                        $daysText = 'Geen deadline beschikbaar';
                        $colorClass = 'gray';
                        $formattedDeadline = '';
                    }

                    // Standaard grijze kleur (wordt hieronder overschreven)
                    $stripeColor   = 'bg-gray-500';
                    $iconBg        = 'bg-gray-50';
                    $iconBorder    = 'border-gray-100';
                    $iconText      = 'text-gray-600';
                    $daysTextColor = 'text-gray-600';

                    // Zet de juiste kleur op basis van de urgentie
                    if ($colorClass === 'red') {
                        $stripeColor   = 'bg-red-500';
                        $iconBg        = 'bg-red-50';
                        $iconBorder    = 'border-red-100';
                        $iconText      = 'text-red-600';
                        $daysTextColor = 'text-red-600';
                    } elseif ($colorClass === 'amber') {
                        $stripeColor   = 'bg-amber-500';
                        $iconBg        = 'bg-amber-50';
                        $iconBorder    = 'border-amber-100';
                        $iconText      = 'text-amber-600';
                        $daysTextColor = 'text-amber-600';
                    } elseif ($colorClass === 'sky') {
                        $stripeColor   = 'bg-sky-500';
                        $iconBg        = 'bg-sky-50';
                        $iconBorder    = 'border-sky-100';
                        $iconText      = 'text-sky-600';
                        $daysTextColor = 'text-sky-600';
                    } elseif ($colorClass === 'violet') {
                        $stripeColor   = 'bg-violet-500';
                        $iconBg        = 'bg-violet-50';
                        $iconBorder    = 'border-violet-100';
                        $iconText      = 'text-violet-600';
                        $daysTextColor = 'text-violet-600';
                    }
                @endphp

                <!-- Één rij per lid -->
                <div class="flex items-center justify-between p-3.5 bg-gray-50/50 border border-gray-100 rounded-xl relative overflow-hidden pl-5 hover:shadow-sm transition-all duration-200">
                    
                    <!-- Gekleurde balk aan de linkerkant -->
                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $stripeColor }}"></div>

                    <div class="flex items-center gap-3">
                        <!-- Icoontje met kleur op basis van urgentie -->
                        <div class="w-9 h-9 rounded-full {{ $iconBg }} border {{ $iconBorder }} flex items-center justify-center {{ $iconText }} shadow-sm">
                            @if($colorClass === 'red')
                                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                            @elseif($colorClass === 'amber')
                                <i class="fa-solid fa-clock text-sm"></i>
                            @else
                                <i class="fa-regular fa-clock text-sm"></i>
                            @endif
                        </div>

                        <div>
                            <!-- Naam van het lid -->
                            <div class="text-sm font-semibold text-gray-800">{{ $lid->gebruiker->naam }}</div>
                            <!-- Hoeveel dagen er nog over zijn -->
                            <div class="text-xs {{ $daysTextColor }} font-semibold mt-0.5" title="Deadline: {{ $formattedDeadline }}">
                                {{ $daysText }}
                                @if($formattedDeadline)
                                    <span class="text-gray-400 font-normal ml-1">· {{ $formattedDeadline }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Bedrag dat het lid moet betalen -->
                    <div class="text-sm font-bold text-gray-900">
                        Srd {{ number_format($lid->MaandelijkseBijdrage(), 2, ',', '.') }}
                    </div>
                </div>

            @empty
                <!-- Geen leden met een deadline gevonden -->
                <div class="flex flex-col items-center justify-center p-6 border border-dashed border-gray-200 rounded-xl bg-gray-50/30 text-center">
                    <div class="w-10 h-10 rounded-full bg-green-50 border border-green-100 flex items-center justify-center text-green-600 mb-3 shadow-sm">
                        <i class="fa-regular fa-circle-check text-base"></i>
                    </div>
                    <div class="text-sm font-medium text-gray-700">Geen aankomende deadlines</div>
                    <div class="text-xs text-gray-500 mt-1">Alle leden zijn up-to-date met hun betalingen.</div>
                </div>
            @endforelse
        </div>

        <!-- Paginaknoppen — alleen tonen als er meer dan één pagina is -->
        @if($deadlineLeden->hasPages())
            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">

                <!-- Knop: vorige pagina -->
                @if($deadlineLeden->onFirstPage())
                    <!-- Op de eerste pagina: knop uitgeschakeld -->
                    <span class="flex items-center gap-1.5 text-xs text-gray-300 cursor-not-allowed font-semibold">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Vorige
                    </span>
                @else
                    <a href="{{ $deadlineLeden->previousPageUrl() }}"
                       class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 font-semibold transition">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Vorige
                    </a>
                @endif

                <!-- Huidige pagina nummer -->
                <span class="text-xs text-gray-400 font-medium">
                    Pagina {{ $deadlineLeden->currentPage() }} van {{ $deadlineLeden->lastPage() }}
                </span>

                <!-- Knop: volgende pagina -->
                @if($deadlineLeden->hasMorePages())
                    <a href="{{ $deadlineLeden->nextPageUrl() }}"
                       class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 font-semibold transition">
                        Volgende <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                @else
                    <!-- Op de laatste pagina: knop uitgeschakeld -->
                    <span class="flex items-center gap-1.5 text-xs text-gray-300 cursor-not-allowed font-semibold">
                        Volgende <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </span>
                @endif

            </div>
        @endif

    </div>
</div>