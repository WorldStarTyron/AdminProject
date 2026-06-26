<div class="bg-white rounded-xl border border-gray-200 shadow-[0_1px_3px_rgba(0,0,0,0.02)] overflow-hidden">

    <!-- Bovenste balk met titel en bladerknoppen -->
    <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100">
        <h3 class="text-base font-bold text-gray-900">
            <!-- Icoontje voor leden -->
            <i class="fa-solid fa-users text-gray-400 mr-2"></i>
            Leden Overzicht
        </h3>
        <div class="flex items-center gap-3.5">
            <!-- Tekst die laat zien hoeveel leden er worden getoond -->
            <span class="text-xs text-gray-500 font-medium">
                Showing {{ $dashboardLeden->firstItem() ?? 0 }} to {{ $dashboardLeden->lastItem() ?? 0 }} of {{ $dashboardLeden->total() }} entries
            </span>

            <!-- Knoppen om naar vorige en volgende pagina te gaan -->
            <div class="flex items-center gap-1.5">

                <!-- Vorige pagina knop -->
                @if($dashboardLeden->onFirstPage())
                    <!-- Op eerste pagina: knop is uitgeschakeld -->
                    <span class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-300 cursor-not-allowed">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </span>
                @else
                    <!-- Klik om naar vorige pagina te gaan -->
                    <a href="{{ $dashboardLeden->previousPageUrl() }}" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition hover:bg-gray-50">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </a>
                @endif

                <!-- Volgende pagina knop -->
                @if($dashboardLeden->hasMorePages())
                    <!-- Klik om naar volgende pagina te gaan -->
                    <a href="{{ $dashboardLeden->nextPageUrl() }}" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition hover:bg-gray-50">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                @else
                    <!-- Op laatste pagina: knop is uitgeschakeld -->
                    <span class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-300 cursor-not-allowed">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </span>
                @endif

            </div>
        </div>
    </div>

    <!-- Tabel met alle leden -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">

            <!-- Kolomtitels bovenaan de tabel -->
            <thead>
                <tr class="text-[11px] font-extrabold tracking-wider text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                    <th class="py-3.5 px-6">
                        <i class="fa-solid fa-hashtag mr-1"></i> Leden ID
                    </th>
                    <th class="py-3.5 px-6">
                        <i class="fa-solid fa-user mr-1"></i> Naam
                    </th>
                    <th class="py-3.5 px-6">
                        <i class="fa-solid fa-envelope mr-1"></i> Email
                    </th>
                    <th class="py-3.5 px-6">
                        <i class="fa-solid fa-phone mr-1"></i> Telefoon
                    </th>
                    <th class="py-3.5 px-6 text-center">
                        <i class="fa-solid fa-circle-check mr-1"></i> Betaling Status
                    </th>
                    <th class="py-3.5 px-6 w-20 text-center">
                        <i class="fa-solid fa-gear mr-1"></i> Acties
                    </th>
                </tr>
            </thead>

            <!-- Rijen met ledengegevens -->
            <tbody class="text-sm divide-y divide-gray-100">

                @forelse($dashboardLeden as $lid)
                    @php
                        // Haal de betaling op voor de huidige maand
                        $currentMonthPayment = $lid->betalingVoorMaand($currentMonth ?? now()->month, $currentYear ?? now()->year);
                        $paymentStatus = $currentMonthPayment?->status;

                        // Controleer wat de betaalstatus is
                        $isPaid = \App\Models\Betaling::isBetaald($paymentStatus);
                        $isOpenstaand = $paymentStatus === 'Openstaand';
                        $isInAfwachting = $paymentStatus === 'in_afwachting';

                        // Kies een kleurverloop voor de avatar op basis van het lid-ID
                        $gradients = [
                            'from-blue-500 to-indigo-600',
                            'from-pink-500 to-rose-600',
                            'from-emerald-400 to-teal-500',
                            'from-amber-400 to-orange-500',
                            'from-violet-500 to-purple-600',
                        ];
                        $gradient = $gradients[$lid->lid_id % count($gradients)];

                        // Maak initialen aan van de naam (bijv. "Jan de Vries" → "JD")
                        $initials = '';
                        if ($lid->gebruiker && $lid->gebruiker->naam) {
                            $nameParts = explode(' ', $lid->gebruiker->naam);
                            $initials = strtoupper(substr($nameParts[0], 0, 1));
                            if (count($nameParts) > 1) {
                                $initials .= strtoupper(substr($nameParts[1], 0, 1));
                            }
                        } else {
                            // Geen naam gevonden
                            $initials = '??';
                        }
                    @endphp

                    <!-- Één rij per lid -->
                    <tr class="hover:bg-gray-50/50 transition-colors">

                        <!-- Lid-ID met opvulling zodat het er zo uitziet: 001, 002, etc. -->
                        <td class="py-4 px-6">
                            <span class="inline-flex px-2 py-0.5 text-[11px] font-extrabold tracking-wide text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-md font-mono">
                                <i class="fa-solid fa-id-badge mr-1 text-indigo-400"></i>
                                {{ str_pad($lid->lid_id, 3, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>

                        <!-- Naam met avatar (rondje met initialen) -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <!-- Gekleurde cirkel met initialen van het lid -->
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr {{ $gradient }} flex items-center justify-center text-[11px] font-extrabold text-white shadow-sm">
                                    {{ $initials }}
                                </div>
                                <span class="font-semibold text-gray-900">{{ $lid->gebruiker->naam ?? '—' }}</span>
                            </div>
                        </td>

                        <!-- E-mailadres van het lid -->
                        <td class="py-4 px-6 text-gray-500 font-medium">
                            <i class="fa-regular fa-envelope mr-1 text-gray-400"></i>
                            {{ $lid->gebruiker->email ?? '—' }}
                        </td>

                        <!-- Telefoonnummer van het lid -->
                        <td class="py-4 px-6 text-gray-500 font-medium">
                            <i class="fa-solid fa-phone mr-1 text-gray-400"></i>
                            {{ $lid->telefoonnummer ?? '—' }}
                        </td>

                        <!-- Betaalstatus badge: kleur hangt af van de status -->
                        <td class="py-4 px-6 text-center">

                            @if($isPaid)
                                <!-- Lid heeft betaald deze maand: groene badge -->
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100/50">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i>
                                    Betaald
                                </span>

                            @elseif($isOpenstaand)
                                <!-- Betaling staat nog open: oranje badge -->
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100/50">
                                    <i class="fa-solid fa-clock text-amber-500 text-[10px]"></i>
                                    Openstaand
                                </span>

                            @elseif($isInAfwachting)
                                <!-- Bewijs is ingediend maar nog niet goedgekeurd: blauwe badge -->
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-100/50">
                                    <i class="fa-solid fa-hourglass-half text-sky-500 text-[10px]"></i>
                                    In afwachting
                                </span>

                            @else
                                <!-- Geen betaling gevonden of niet betaald: rode badge -->
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100/50">
                                    <i class="fa-solid fa-circle-xmark text-red-500 text-[10px]"></i>
                                    Niet betaald
                                </span>

                            @endif
                        </td>

                        <!-- Knop om het lidprofiel te openen -->
                        <td class="py-4 px-6 text-center">
                            <a href="{{ route('ledenpagina.show', $lid->lid_id) }}"
                               class="inline-block text-gray-400 hover:text-blue-600 p-1 rounded-lg hover:bg-blue-50 transition"
                               title="Bekijk lidprofiel">
                                <i class="fa-solid fa-eye text-base"></i>
                            </a>
                        </td>

                    </tr>

                @empty
                    <!-- Geen leden gevonden in de database -->
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-400">
                            <i class="fa-solid fa-users-slash text-2xl mb-2 block"></i>
                            Geen leden gevonden in het systeem.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- Onderste balk met link naar alle leden -->
    <div class="bg-gray-50 border-t border-gray-100 px-6 py-4 flex items-center justify-center">
        <a href="{{ route('ledenpagina') }}" class="text-xs text-gray-600 hover:text-gray-900 font-bold tracking-wide uppercase transition">
            <i class="fa-solid fa-arrow-right mr-1"></i>
            Bekijk alle leden
        </a>
    </div>

</div>