<!-- Tabel: geschiedenis van alle betalingen met filter op maand/jaar -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">

    <!-- Header met titel en filter dropdowns -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                <i class="fa-solid fa-clock-rotate-left text-sm"></i>
            </div>
            <h2 class="text-base font-bold text-slate-900 m-0">Geschiedenis Betaling</h2>
        </div>

        <form method="GET" action="{{ route('GegevensPagina') }}" class="flex items-center gap-2">
            <!-- Maand dropdown -->
            <select name="maand" onchange="this.form.submit()"
                class="text-xs px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Alle maanden</option>
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maart','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Augustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'December'] as $num => $naam)
                    <option value="{{ $num }}" {{ request('maand') == $num ? 'selected' : '' }}>{{ $naam }}</option>
                @endforeach
            </select>

            <!-- Jaar dropdown -->
            <select name="jaar" onchange="this.form.submit()"
                class="text-xs px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Alle jaren</option>
                @foreach($beschikbareJaren as $jaar)
                    <option value="{{ $jaar }}" {{ request('jaar') == $jaar ? 'selected' : '' }}>{{ $jaar }}</option>
                @endforeach
            </select>

            <!-- Wis filter (alleen tonen als er een filter actief is) -->
            @if(request('maand') || request('jaar'))
                <a href="{{ route('GegevensPagina') }}"
                   class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 hover:bg-slate-50 transition" title="Wis filter">
                    <i class="fa-solid fa-xmark text-slate-400 text-sm"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Betaalgeschiedenis tabel -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Datum</th>
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Bedrag</th>
                    <th class="text-center text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Status</th>
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Bon Nummer</th>
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Maand/Jaar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($betalingen as $betaling)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-slate-600">{{ \Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('d M Y') }}</td> <!-- Datum -->
                        <td class="px-6 py-4 text-slate-800 font-semibold">SRD {{ number_format($betaling->bedrag, 2, ',', '.') }}</td> <!-- Bedrag -->
                        <td class="px-6 py-4 text-center">
                            @if($betaling->status === 'betaald' || $betaling->status === 'goed_gekeurd') <!-- Status -->
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600">Betaald</span>
                            @elseif($betaling->status === 'niet_betaald')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600">Niet Betaald</span>
                            @elseif($betaling->status === 'Openstaand')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-600">Openstaand</span>
                            @elseif($betaling->status === 'in_afwachting')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-600">In Afwachting</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">{{ ucfirst($betaling->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $betaling->bon ? $betaling->bon->bon_nummer : '—' }}</td> <!-- Bon Nummer -->
                        <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                            @if($betaling->maand == '1') Januari @endif
                            @if($betaling->maand == '2') Februari @endif
                            @if($betaling->maand == '3') Maart @endif
                            @if($betaling->maand == '4') April @endif
                            @if($betaling->maand == '5') Mei @endif
                            @if($betaling->maand == '6') Juni @endif
                            @if($betaling->maand == '7') Juli @endif
                            @if($betaling->maand == '8') Augustus @endif
                            @if($betaling->maand == '9') September @endif
                            @if($betaling->maand == '10') Oktober @endif
                            @if($betaling->maand == '11') November @endif
                            @if($betaling->maand == '12') December @endif
                            {{ $betaling->jaar }}
                        </td> <!-- Maand/Jaar -->
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                            Geen betalingsgeschiedenis gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginering -->
    @if($betalingen->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
            <p class="text-xs text-slate-400">
                Weergeven {{ $betalingen->firstItem() }}–{{ $betalingen->lastItem() }} van {{ $betalingen->total() }}
            </p>
            <div>{{ $betalingen->links('pagination::tailwind') }}</div>
        </div>
    @else
        <div class="px-6 py-3 border-t border-slate-100 text-center">
            <a href="#" class="text-xs font-semibold text-slate-500 hover:text-slate-700">Bekijk volledige historie</a>
        </div>
    @endif
</div>
