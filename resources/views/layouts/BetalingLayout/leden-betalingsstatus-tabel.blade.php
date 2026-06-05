<div class="bg-white rounded-2xl border border-slate-200/70 overflow-hidden flex flex-col h-full">
    {{-- Header --}}
    <div class="px-6 pt-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h2 class="text-[0.9375rem] font-bold text-slate-800 tracking-tight">Leden Betalingsstatus</h2>
            <p class="text-xs text-slate-400 mt-0.5 font-medium">Overzicht per lid voor geselecteerde periode</p>
        </div>

        <!-- Month/year filter -->
        <form method="GET" action="{{ route('betalingPagina') }}" class="flex items-center gap-1.5">
            <select name="maand" class="border border-slate-200 bg-slate-50 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ ($maand ?? now()->month) == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="jaar" class="border border-slate-200 bg-slate-50 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ ($jaar ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto flex-1">
        <table class="w-full text-left table-fixed">
            <colgroup>
                <col style="width: 3%">
                <col style="width: 2%">
                <col style="width: 2%">
                <col style="width: 2%">
            </colgroup>
            <thead>
                <tr class="bg-slate-50/80">
                    <th class="px-5 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider">Naam</th>
                    <th class="px-5 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3 text-[0.625rem] font-bold text-slate-400 uppercase tracking-wider text-right">Contributiebedrag</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100/80">
                @forelse($ledenStatus as $item)
                    @php
                        $statusConfig = match($item->status) {
                            'betaald'      => ['dot' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/80', 'label' => 'Betaald'],
                            'niet_betaald' => ['dot' => 'bg-red-500',     'badge' => 'bg-red-50 text-red-600 ring-1 ring-red-200/80',       'label' => 'Niet betaald'],
                            'Openstaand'   => ['dot' => 'bg-amber-500',   'badge' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200/80', 'label' => 'Openstaand'],
                            default        => ['dot' => 'bg-slate-400',   'badge' => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',   'label' => ucfirst($item->status)],
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors group">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[0.625rem] font-bold text-slate-500 shrink-0">
                                    {{ strtoupper(substr($item->naam, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800 text-[0.8125rem] truncate">{{ $item->naam }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.625rem] font-bold tracking-wide {{ $statusConfig['badge'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }} shrink-0"></span>
                                {{ $statusConfig['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-[0.8125rem] text-slate-500 truncate">{{ $item->lid_type }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="font-bold text-slate-800 text-[0.8125rem]">SRD {{ number_format($item->maandelijks_bijdrage, 2) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                </div>
                                <span class="text-sm text-slate-400 font-medium">Geen leden gevonden</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>