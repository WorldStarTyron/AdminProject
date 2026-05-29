<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapportage - Simpel overzicht</title>
    <!-- Font Awesome & Chart.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css'])
</head>
<body class="bg-slate-100 font-sans antialiased">

<div class="flex min-h-screen">
    @include('layouts.Sidebars.sidebar')

    <div class="flex-1 ml-0 md:ml-64 transition-all duration-300">
        @include('layouts.header')

        <main class="p-6 md:p-8">
            {{-- Broodkruimel --}}
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-4">
                <a href="/dashboard" class="hover:text-blue-500">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-medium">Rapportage</span>
            </div>

            {{-- Kop en periodefilter --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Rapportage</h1>
                <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-1.5 shadow-sm text-sm">
                    <span class="text-slate-400">Periode:</span>
                    <input type="date" id="date-from" value="{{ request('from', date('Y-01-01')) }}" class="border-none p-0 text-slate-700 bg-transparent focus:outline-none">
                    <span class="text-slate-300">—</span>
                    <input type="date" id="date-to" value="{{ request('to', date('Y-12-31')) }}" class="border-none p-0 text-slate-700 bg-transparent focus:outline-none">
                </div>
            </div>

            {{-- 3 KPI-kaarten (simplistisch) --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-euro-sign"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Totale inkomsten</div>
                            <div class="text-xl font-bold text-slate-800">€ {{ number_format($totaleInkomsten ?? 24850, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Openstaand bedrag</div>
                            <div class="text-xl font-bold text-slate-800">€ {{ number_format($openstaandBedrag ?? 1340, 0, ',', '.') }}</div>
                            <div class="text-xs text-slate-500">{{ $openstaandAantal ?? 8 }} leden</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Totaal leden</div>
                            <div class="text-xl font-bold text-slate-800">{{ number_format($totaalLeden ?? 182, 0, ',', '.') }}</div>
                            <div class="text-xs text-slate-500">+{{ $nieuwDitKwartaal ?? 9 }} dit kwartaal</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Eenvoudige grafiek: inkomsten per kwartaal --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-8">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-semibold text-slate-700">Inkomsten per kwartaal</h2>
                    <div class="flex gap-3 text-xs">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-blue-500"></span>Contributie</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-emerald-500"></span>Activiteiten</span>
                    </div>
                </div>
                <div class="h-56 w-full">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>

            <!-- Simpele tabel: recente betalingen -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center">
                    <h2 class="font-semibold text-slate-700">Recente betalingen</h2>
                    <a href="{{ route('betalingen.export') ?? '#' }}" class="text-xs text-blue-600 hover:underline">Bekijk alle &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3 text-left">Lid</th>
                                <th class="px-5 py-3 text-left">Bedrag</th>
                                <th class="px-5 py-3 text-left">Datum</th>
                                <th class="px-5 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($betalingen ?? [] as $b)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-2.5">{{ $b->naam }}</td>
                                <td class="px-5 py-2.5 font-medium">€ {{ number_format($b->bedrag, 2, ',', '.') }}</td>
                                <td class="px-5 py-2.5 text-slate-500">{{ \Carbon\Carbon::parse($b->datum)->format('d-m-Y') }}</td>
                                <td class="px-5 py-2.5">
                                    @php
                                        $statusClass = match($b->status_label) {
                                            'Betaald' => 'bg-emerald-100 text-emerald-700',
                                            'In behandeling' => 'bg-amber-100 text-amber-700',
                                            'Niet betaald' => 'bg-red-100 text-red-700',
                                            default => 'bg-slate-100 text-slate-600'
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">{{ $b->status_label }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-5 py-4 text-center text-slate-400">Geen betalingen in deze periode</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div> 

@vite('resources/js/Rapportage-Chart.js')
</body>
</html>