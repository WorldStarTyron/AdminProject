<!-- Tabel: openstaande maanden met selectievakjes voor batch betaling -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">

    <!-- Header met titel en 'Selecteer alles' knop -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <h2 class="text-base font-bold text-slate-900 m-0">Openstaande Maanden</h2>
        <button type="button"
                onclick="document.querySelectorAll('.openstaand-checkbox').forEach(c => { c.checked = true; c.dispatchEvent(new Event('change')); })"
                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
            Selecteer alles
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="px-6 py-3 w-10"></th>
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-3 py-3">Maand / Periode</th>
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-3 py-3">Vervaldatum</th>
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-3 py-3">Bedrag</th>
                    <th class="text-right text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($openstaandeMaanden as $maandBetaling)
                    @php
                        // Laatste dag van de maand als vervaldatum
                        $vervaldatum = \Carbon\Carbon::createFromDate($maandBetaling->jaar, $maandBetaling->maand, 1)->endOfMonth();
                        $maandNaam = \Carbon\Carbon::createFromDate($maandBetaling->jaar, $maandBetaling->maand, 1)->translatedFormat('F Y');
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox"
                                   class="openstaand-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                   data-bedrag="{{ $maandBetaling->bedrag }}"
                                   data-id="{{ $maandBetaling->betaling_id }}"
                                   onchange="updateSelectie()">
                        </td>
                        <td class="px-3 py-4">
                            <div class="text-sm font-semibold text-slate-900">{{ $maandNaam }}</div>
                            <div class="text-xs text-slate-400">Maandelijkse Contributie</div>
                        </td>
                        <td class="px-3 py-4 text-slate-600">{{ $vervaldatum->translatedFormat('d M Y') }}</td>
                        <td class="px-3 py-4 font-semibold text-slate-800">SRD {{ number_format($maandBetaling->bedrag, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($maandBetaling->status === 'niet_betaald')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600">Niet Betaald</span>
                            @elseif($maandBetaling->status === 'Openstaand')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-600">Openstaand</span>
                            @elseif($maandBetaling->status === 'in_afwachting')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-600">In Afwachting</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                            Geen openstaande maanden. U bent volledig bij!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
            <!-- Paginate Section-->
            <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-center">
                    {{ $openstaandeMaanden->links() }}
                </div>
            </div>
    </div>
</div>
