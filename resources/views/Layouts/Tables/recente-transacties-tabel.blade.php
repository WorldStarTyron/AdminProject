<!-- Buitenste kaart wrapper -->
<div class="bg-white rounded-2xl border border-slate-200/70 overflow-hidden flex flex-col h-full">

    <!-- Bovenste balk met titel en knoppen -->
    <div class="px-6 pt-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <!-- Titel en ondertitel -->
            <h2 class="text-[0.9375rem] font-bold text-slate-800 tracking-tight">Recente Transacties</h2>
            <p class="text-xs text-slate-400 mt-0.5 font-medium">Laatste betalingen en mutaties</p>
        </div>

        <!-- Knoppen alleen zichtbaar als de gebruiker betalingen mag beheren -->
        @can('betalingen-beheren')
        <div class="flex gap-2">


            <!-- Zoekbalk: filtert ledenstatus tabel op naam, email of telefoon -->
            <form action="{{ route('betalingPagina') }}" method="GET" class="flex items-center gap-2">
                <!-- maand/jaar mee in de URL houden zodat de huidige periode behouden blijft -->
                <input type="hidden" name="maand" value="{{ $maand ?? now()->month }}">
                <input type="hidden" name="jaar"  value="{{ $jaar ?? now()->year }}">

                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Zoek lid..."
                       class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">

                <button type="submit"
                        class="bg-slate-800 hover:bg-slate-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-[10px] mr-1"></i>Zoek
                </button>
            </form>





            <!-- Knop om een nieuwe betaling toe te voegen -->
            <button id="openBetalingModal" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold px-3.5 py-1.5 rounded-lg transition-all duration-150 cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-plus text-[0.625rem]"></i>
                Voeg Betaling
            </button>
        </div>
        @endcan
    </div>

    <!-- Tabel met betalingen, scrollbaar op kleine schermen -->
    <div class="overflow-x-auto flex-1">
        <table class="w-full text-left table-fixed">

            <!-- Breedte per kolom -->
            <colgroup>
                @can('betalingen-beheren')
                    <col style="width: 22%">
                    <col style="width: 18%">
                    <col style="width: 14%">
                    <col style="width: 18%">
                    <col style="width: 18%">
                    <col style="width: 10%">
                @else
                    <col style="width: 25%">
                    <col style="width: 20%">
                    <col style="width: 15%">
                    <col style="width: 20%">
                    <col style="width: 20%">
                @endcan
            </colgroup>

            <!-- Kolomkoppen -->
            <thead>
                <tr class="bg-slate-50/80">
                    <th class="px-4 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider">Naam</th>
                    <th class="px-4 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider">Methode</th>
                    <th class="px-4 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider">Bedrag</th>
                    <th class="px-4 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider">Datum</th>
                    <th class="px-4 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider">Bonnummer</th>
                    @can('betalingen-beheren')
                    <th class="px-4 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider text-center">Acties</th>
                    @endcan
                </tr> 
            </thead>

            <!-- Rijen met betalingen -->
            <tbody class="divide-y divide-slate-100/80">
                @forelse($recenteBetalingen as $betaling)
                <tr class="hover:bg-slate-50/60 transition-colors group">

                    <!-- Naam met beginletter avatar -->
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-50 flex items-center justify-center text-[0.5625rem] font-bold text-blue-600 shrink-0">
                                {{ strtoupper(substr($betaling->naam, 0, 1)) }}
                            </div>
                            <span class="font-semibold text-slate-800 text-[0.8125rem] truncate">{{ $betaling->naam }}</span>
                        </div>
                    </td>

                    <!-- Betaalmethode als label, of streepje als leeg -->
                    <td class="px-4 py-3.5">
                        @if($betaling->methode)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[0.625rem] font-semibold bg-slate-100 text-slate-600">
                                {{ $betaling->methode }}
                            </span>
                        @else
                            <span class="text-slate-300 text-sm">—</span>
                        @endif
                    </td>

                    <!-- Bedrag in SRD -->
                    <td class="px-4 py-3.5">
                        <span class="font-bold text-slate-800 text-[0.8125rem]">SRD {{ number_format($betaling->bedrag, 2) }}</span>
                    </td>

                    <!-- Datum van de betaling -->
                    <td class="px-4 py-3.5 text-[0.8125rem] text-slate-500 truncate">
                        {{ Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('j M Y') }}
                    </td>

                    <!-- Bonnummer als die er is, anders streepje -->
                    <td class="px-4 py-3.5">
                        @if($betaling->bon?->bon_nummer)
                            <span class="font-mono text-[0.75rem] text-slate-500 bg-slate-50 border border-slate-200/60 px-2 py-0.5 rounded-md truncate">
                                {{ $betaling->bon->bon_nummer }}
                            </span>
                        @else
                            <span class="text-slate-300 text-sm">—</span>
                        @endif
                    </td>

                    <!-- Acties: Bewerken & Verwijderen, alleen zichtbaar bij hoveren -->
                    @can('betalingen-beheren')
                    <td class="px-4 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-150">
                            <!-- Bewerkknop -->
                            <button
                                class="edit-betaling-btn w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-400 flex items-center justify-center transition-all duration-150"
                                data-id="{{ $betaling->betaling_id }}"
                                data-naam="{{ $betaling->naam }}"
                                data-datum="{{ Carbon\Carbon::parse($betaling->ingediend_op)->format('Y-m-d') }}"
                                data-bedrag="{{ $betaling->bedrag }}"
                                data-status="{{ $betaling->status }}"
                                data-methode="{{ $betaling->methode }}"
                                title="Bewerken">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            @endcan


                            @can('betalingen-beheren')
                            <!-- Verwijderknop -->
                            <form action="{{ route('betalingen.destroy', $betaling->betaling_id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="delete-betaling-btn w-7 h-7 rounded-lg bg-red-100 hover:bg-red-50 hover:text-red-600 text-red-400 flex items-center justify-center transition-all duration-150"
                                    title="Verwijderen"
                                    onclick="return confirm('Weet je zeker dat je deze betaling wilt verwijderen? Dit kan niet ongedaan gemaakt worden.')">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        @endcan

                        
                        </div>
                    </td>
                </tr>

                <!-- Lege staat als er geen betalingen zijn -->
                @empty
                <tr>
                    <td colspan="@can('betalingen-beheren') 6 @else 5 @endcan" class="px-5 py-10 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                            </div>
                            <span class="text-sm text-slate-400 font-medium">Geen transacties gevonden</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginering onderaan als er meerdere paginas zijn -->
    @if($recenteBetalingen instanceof \Illuminate\Pagination\LengthAwarePaginator && $recenteBetalingen->hasPages())
    <div class="px-5 py-3 border-t border-slate-100 flex justify-between items-center bg-slate-50/60">
        <!-- Teller zoals: 1-10 van 34 -->
        <p class="text-xs text-slate-400 font-medium">
            {{ $recenteBetalingen->firstItem() }}–{{ $recenteBetalingen->lastItem() }} van {{ $recenteBetalingen->total() }}
        </p>
        {{ $recenteBetalingen->links() }}
    </div>
    @endif
</div>