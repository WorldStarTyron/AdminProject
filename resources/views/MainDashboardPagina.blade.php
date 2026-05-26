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
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/Toevoegen.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-[#F8F9FA] text-slate-800 font-['Inter']">
    <div class="flex min-h-screen">
        @include('layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('layouts.header')

            <main class="p-6 max-w-[1500px] mx-auto space-y-6">
                <!-- Stats Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <!-- Total Income -->
                    <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-sm font-medium text-slate-600">Total Income</h3>
                            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white">
                                <i class="fa-solid fa-dollar-sign text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-900">Srd 33,321.50</p>
                            <p class="text-xs text-slate-400 mt-1">elk maand</p>
                        </div>
                    </div>

                    <!-- Total Leden -->
                    <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-sm font-medium text-slate-600">Total Leden</h3>
                            <div class="w-8 h-8 flex items-center justify-center text-slate-600">
                                <i class="fa-solid fa-users text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-900">65</p>
                            <p class="text-xs text-slate-400 mt-1">+3 392</p>
                        </div>
                    </div>

                    <!-- Totaal Betaald -->
                    <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-sm font-medium text-slate-600">Totaal Betaald</h3>
                            <div class="w-8 h-8 flex items-center justify-center text-slate-600">
                                <i class="fa-solid fa-user-check text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-900">59</p>
                            <p class="text-xs text-slate-400 mt-1">-1.22%</p>
                        </div>
                    </div>

                    <!-- Niet Betaald -->
                    <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-sm font-medium text-slate-600">Niet Betaald</h3>
                            <div class="w-8 h-8 flex items-center justify-center text-slate-600">
                                <i class="fa-solid fa-user-xmark text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-900">6</p>
                            <p class="text-xs text-slate-400 mt-1">-1.22%</p>
                        </div>
                    </div>

                </div>

                <!-- Chart Section -->
                <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-base font-semibold text-slate-800">Maandelijke preformance</h3>
                        <button class="text-slate-400 hover:text-slate-600 bg-slate-50 p-1.5 rounded-md">
                            <i class="fa-solid fa-gear text-base"></i>
                        </button>
                    </div>
                    <div id="performanceChart" class="w-full h-[300px]"></div>
                </div>

                @include('layouts.Main_Dashboard Layouts.Main_Table')
            </main>
        </div>
    </div>

    <!-- Script -->
    @vite('resources/js/MainDashboard-chart.js')

</body>
</html>