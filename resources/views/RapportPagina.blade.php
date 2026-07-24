<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rapportage | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/UI/Sidebar.js'])
</head>
<body class="m-0 bg-[#f0f4f8] text-slate-700 font-['Inter',sans-serif] antialiased">

<!-- ==========================================================================
     MAIN WRAPPER: Contains sidebar navigation and main content area
     ========================================================================== -->
<div class="flex min-h-screen">
    <!-- Include sidebar layout -->
    @include('Layouts.Sidebars.sidebar')

    <!-- Main page content container (shifts right to account for sidebar) -->
    <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
        <!-- Include header bar layout -->
        @include('Layouts.Headers.header')

        <!-- Inside main space -->
        <main class="p-6 md:p-8 max-w-[1600px] mx-auto space-y-6">

            <!-- ==========================================================================
                 HEADER SECTION: Page title, breadcrumbs, and date-range filter
                 ========================================================================== -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <!-- Breadcrumbs -->
                    <nav class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                        <a href="{{ route('MainDashboardPagina') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        <span class="text-slate-600 font-medium">Rapportage</span>
                    </nav>
                    <!-- Title & Subtitle -->
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rapportage & Inzichten</h1>
                    <p class="text-sm text-slate-400 mt-1">Analyseer inkomsten, openstaande bedragen en ledengroei.</p>
                </div>
                
                <!-- Period Date Filter -->
                <div class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-sm">
                    <div class="flex items-center gap-1 text-slate-400 text-xs">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span class="font-medium">Periode:</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <!-- De pagina laadt automatisch opnieuw als je een datum verandert -->
                        <input type="date" id="date-from" value="{{ $from }}" max="{{ date('Y-m-d') }}" class="border-none p-0 text-slate-700 bg-transparent focus:outline-none font-semibold text-xs cursor-pointer">
                        <span class="text-slate-300">—</span>
                        <input type="date" id="date-to" value="{{ $to }}" max="{{ date('Y-m-d') }}" class="border-none p-0 text-slate-700 bg-transparent focus:outline-none font-semibold text-xs cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- ==========================================================================
                 KPI STATS CARDS: Overview of key metrics
                 ========================================================================== -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: Totale Inkomsten -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] p-6 flex items-start justify-between">
                    <div class="space-y-3">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Totale Inkomsten</span>
                        <div class="space-y-1">
                            <h3 class="text-3xl font-extrabold text-slate-950 tracking-tight">Srd {{ number_format($totaleInkomsten ?? 0, 2, ',', '.') }}</h3>
                            <span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-medium">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                                +12.4% vs. vorig jaar
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-money-bill-trend-up text-lg"></i>
                    </div>
                </div>

                <!-- Card 2: Openstaand Bedrag -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] p-6 flex items-start justify-between">
                    <div class="space-y-3">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Openstaand Bedrag</span>
                        <div class="space-y-1">
                            <h3 class="text-3xl font-extrabold text-slate-950 tracking-tight">Srd {{ number_format($openstaandBedrag ?? 1340, 2, ',', '.') }}</h3>
                            <span class="inline-flex items-center gap-1 text-xs text-amber-600 font-medium">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $openstaandAantal ?? 8 }} Leden openstaand
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    </div>
                </div>

                <!-- Card 3: Totaal Aantal Leden -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] p-6 flex items-start justify-between">
                    <div class="space-y-3">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Totaal Leden</span>
                        <div class="space-y-1">
                            <h3 class="text-3xl font-extrabold text-slate-950 tracking-tight">{{ number_format($totaalLeden ?? 182, 0, ',', '.') }}</h3>
                            <span class="inline-flex items-center gap-1 text-xs text-blue-600 font-medium">
                                <i class="fa-solid fa-user-plus"></i>
                                +{{ $nieuwDitKwartaal ?? 9 }} Dit kwartaal
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- ==========================================================================
                 CHARTS & SIDE STATS: Graphical charts and visual metrics breakdown
                 ========================================================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left: Chart Card (Uses 2/3 width) -->
                <!-- Deze kaart laat de grafiek met inkomsten zien -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] p-6 lg:col-span-2 space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-slate-50">
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">Inkomsten per maand</h3>
                            <p class="text-xs text-slate-400 font-medium">Vergelijking tussen overboekingen en fysieke betalingen van de geselecteerde periode.</p>
                        </div>
                        <!-- Legend keys -->
                        <!-- Dit is de legenda die uitlegt wat de kleuren in de grafiek betekenen -->
                        <div class="flex gap-4 text-xs font-medium text-slate-500">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-blue-500 inline-block shadow-sm"></span>Overmaking</span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-emerald-500 inline-block shadow-sm"></span>Fysieke inname</span>
                        </div>
                    </div>
                    <!-- Chart canvas target -->
                    <div class="h-72 w-full relative">
                        <canvas id="incomeChart" data-labels="{{ json_encode($chartLabels) }}" data-overmaking="{{ json_encode($chartOvermaking) }}" data-fysiek="{{ json_encode($chartFysiek) }}"></canvas>
                    </div>
                </div>

                <!-- Right: Side Insights & Progress Bars (Uses 1/3 width) -->
                <!-- Dit is de rechterkant met extra informatie en voortgangsbalken -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] p-6 flex flex-col justify-between space-y-6">
                    
                    <!-- Insight Section 1: Payment coverage percentage -->
                    <!-- Hier laten we zien hoeveel procent van de verwachte contributie al binnen is -->
                    <div class="space-y-4">
                        <div class="pb-2 border-b border-slate-50">
                            <h3 class="font-bold text-slate-800 text-base">Betalingsdekkingsgraad</h3>
                            <p class="text-xs text-slate-400">Status van de inning van deze periode.</p>
                        </div>
                        
                        <!-- Progress bar -->
                        <!-- Dit is de voortgangsbalk die meegroeit met het betaalde percentage -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-baseline text-sm">
                                <span class="font-medium text-slate-600">Betaald percentage</span>
                                <span class="font-bold text-emerald-600">{{ number_format($dekkingsgraad, 1, ',', '.') }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden shadow-inner flex">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $dekkingsgraad }}%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Doel: 100%</span>
                                <span>Srd {{ number_format($totaleInkomsten, 0, ',', '.') }} van Srd {{ number_format($totaalVerwacht, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Insight Section 2: Distribution of payment methods -->
                    <!-- Hier laten we zien hoe leden het liefst betalen (overmaken of fysiek) -->
                    <div class="space-y-4">
                        <div class="pb-2 border-b border-slate-50">
                            <h3 class="font-bold text-slate-800 text-base">Betaalmethoden verdeling</h3>
                            <p class="text-xs text-slate-400">Voorkeur van betalingen bij leden.</p>
                        </div>

                        <!-- Payment Method progress lines -->
                        <!-- Dit zijn de balkjes die laten zien hoeveel procent per betaalmethode is gedaan -->
                        <div class="space-y-3">
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs font-semibold text-slate-600">
                                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-building-columns text-slate-400"></i> Overmaking</span>
                                    <span>{{ $overmakingPercentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-blue-500 h-full rounded-full" style="width: {{ $overmakingPercentage }}%"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs font-semibold text-slate-600">
                                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-hand-holding-dollar text-slate-400"></i> Fysieke inname</span>
                                    <span>{{ $fysiekPercentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $fysiekPercentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================================================
                 TABLE SECTION: Lists recent payments
                 ========================================================================== -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] overflow-hidden">
                <!-- Table Header info -->
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Recente Betalingen</h3>
                        <p class="text-xs text-slate-400">Lijst met recent ontvangen betalingen.</p>
                    </div>
                    <!-- Go to all payments -->
                    <a href="{{ route('betalingPagina') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 hover:underline transition-all">
                        Bekijk alle betalingen <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

                <!-- Responsive Table Wrapper -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <th class="px-6 py-4">Lid</th>
                                <th class="px-6 py-4">Bedrag</th>
                                <th class="px-6 py-4">Datum</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <!-- Loop over payments passed from controller -->
                            @forelse($betalingen ?? [] as $b)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <!-- Lid profile & initials bubble -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                            {{ strtoupper(substr($b->naam, 0, 2)) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-slate-900">{{ $b->naam }}</span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Payment Amount -->
                                <td class="px-6 py-4 font-semibold text-slate-800">Srd {{ number_format($b->bedrag, 2, ',', '.') }}</td>
                                <!-- Date received -->
                                <td class="px-6 py-4 text-slate-400 text-xs">
                                    <i class="fa-regular fa-clock mr-1"></i>
                                    {{ \Carbon\Carbon::parse($b->datum)->translatedFormat('d M Y') }}
                                </td>
                                <!-- Payment Status Pill Badge -->
                                <td class="px-6 py-4">
                                    @php
                                        // Dynamically choose badge styling based on payment status label
                                        $badgeStyles = match($b->status_label) {
                                            'Betaald'        => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10',
                                            'In behandeling' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/10',
                                            'Niet betaald'   => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/10',
                                            default          => 'bg-slate-50 text-slate-600 ring-1 ring-slate-600/10'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeStyles }}">
                                        {{ $b->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <!-- Rendered when there are no payments to list -->
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fa-regular fa-folder-open text-3xl text-slate-300"></i>
                                        <span class="font-medium text-slate-400">Geen recente betalingen gevonden</span>
                                        <span class="text-xs text-slate-300">Pas de periodefilter aan of voeg nieuwe betalingen toe.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div> 

<!-- Render scripts for the chart -->
@vite('resources/js/Charts/Rapportage-Chart.js')
</body>
</html>