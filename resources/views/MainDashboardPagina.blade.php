<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Main Dashboard - Beheer alle leden en betalingen in het administratie systeem">
    <title>Main Dashboard | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/Toevoegen.css', 'resources/js/UI/Sidebar.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-[#F8F9FA] text-slate-800 font-['Inter']">
    <div class="flex min-h-screen">
        @include('Layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('Layouts.Headers.header')

<main class="p-6 max-w-[1500px] mx-auto space-y-6">
                @include('Layouts.Shared.flash-messages')

                <!-- Header Title Section -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Performance Overview</h1>
                    <p class="text-sm text-gray-500 mt-1">Welcome back. Here's what's happening today.</p>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    <!-- Card 1: TOTAL INCOME -->
                    <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 text-white rounded-xl p-5 flex flex-col justify-between h-full shadow-lg shadow-indigo-950/15 border-0">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-extrabold tracking-wider text-indigo-200/80 uppercase">TOTAL INCOME</span>
                            <div class="w-8 h-8 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center text-white">
                                <i class="fa-solid fa-dollar-sign text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-[11px] text-indigo-300/80 font-semibold mb-0.5">SRD</div>
                            <div class="text-3xl font-bold text-white tracking-tight leading-none mb-3">{{ number_format($totaleInkomsten, 2, ',', '.') }}</div>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-bold font-mono">+12%</span>
                                <span class="text-xs text-indigo-200/80">elk maand</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: TOTAL LEDEN -->
                    <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-700 text-white rounded-xl p-5 flex flex-col justify-between h-full shadow-lg shadow-indigo-900/15 border-0">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-extrabold tracking-wider text-blue-100 uppercase">TOTAL LEDEN</span>
                            <div class="w-8 h-8 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center text-white">
                                <i class="fa-solid fa-users text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-white tracking-tight leading-none mb-3">{{$totaalLeden}}</div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-emerald-300 font-bold bg-white/10 px-1.5 py-0.5 rounded font-mono">+2,392</span>
                                <span class="text-xs text-blue-100/90">since last period</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: TOTAAL BETAALD -->
                    <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white rounded-xl p-5 flex flex-col justify-between h-full shadow-lg shadow-emerald-950/15 border-0">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-extrabold tracking-wider text-emerald-100 uppercase">TOTAAL BETAALD</span>
                            <div class="w-8 h-8 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center text-white">
                                <i class="fa-regular fa-circle-check text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-white tracking-tight leading-none mb-3">{{$totaalBetaald}}</div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-emerald-200 font-bold bg-white/10 px-1.5 py-0.5 rounded font-mono">-1.22%</span>
                                <span class="text-xs text-emerald-100/90">vs monthly avg</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: NIET BETAALD -->
                    <div class="bg-gradient-to-br from-rose-500 via-rose-600 to-red-700 text-white rounded-xl p-5 flex flex-col justify-between h-full shadow-lg shadow-rose-950/15 border-0">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-extrabold tracking-wider text-rose-100 uppercase">NIET BETAALD</span>
                            <div class="w-8 h-8 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center text-white">
                                <i class="fa-regular fa-circle-xmark text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white tracking-tight leading-none mb-3">{{$totaalNietBetaald}}</div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-rose-200 font-bold bg-white/10 px-1.5 py-0.5 rounded font-mono">-1.22%</span>
                                <span class="text-xs text-rose-100/90">unpaid this cycle</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Chart and Upcoming Payments Section-2 -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Monthly Performance Chart (takes 2 cols) -->
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200/80 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">

                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-base font-semibold text-slate-800">Maandelijkse performance</h3>
                            <div class="flex items-center gap-3">
                                <!-- Jaar dropdown -->
                                <select id="filterJaar" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-gray-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all cursor-pointer">
                                    <option value="">Alle jaren</option>
                                </select>

                                <!-- Maand dropdown -->
                                <select id="filterMaand" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-gray-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all cursor-pointer">
                                    <option value="">Alle maanden</option>
                                </select>
                            </div>
                        </div>

                        <!--hier komt mijn grafiek-->
                        <div id="performanceChart" class="w-full h-[300px]"></div>

                    </div>

                    <!-- Right: Aankomende Betalingen-->
                    @include('Layouts.Shared.AankomendeBetaling')

                </div>

                <!--MainDashboard table-->
                @include('Layouts.Tables.Main_Table')
            </main>
        </div>
    </div>

    <!-- Script -->
    @vite('resources/js/Charts/MainDashboard-chart.js')

</body>
</html>
