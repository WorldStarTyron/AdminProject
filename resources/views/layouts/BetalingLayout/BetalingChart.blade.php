<div class="w-full bg-white border border-slate-100 rounded-2xl p-5">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
        <div>
            <p class="text-xs text-slate-400 mb-0.5">Overzicht</p>
            <p class="text-base font-medium text-slate-800">Betalingen per dag</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Maand --}}
            <select id="maand" class="text-sm px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-teal-500">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->locale('nl')->monthName }}
                    </option>
                @endfor
            </select>

            {{-- Jaar --}}
            <select id="jaar" class="text-sm px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-teal-500">
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex items-center gap-2 mb-3">
        <span class="w-2.5 h-2.5 rounded-sm bg-teal-500 inline-block"></span>
        <span class="text-xs text-slate-400">Betalingen</span>
    </div>

    {{-- ApexCharts render target --}}
    <div id="contributieChart" class="w-full min-h-[220px]"></div>
</div>