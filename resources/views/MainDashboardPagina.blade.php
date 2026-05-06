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
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/Toevoegen.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-[#F8F9FA] text-slate-800 font-['Inter']">
    <div class="flex min-h-screen">
        @include('layouts.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300">
            @include('layouts.header')

            <main class="p-6 max-w-[1400px] mx-auto space-y-6">
                <!-- Stats Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Income -->
                    <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-sm font-medium text-slate-600">Total Income</h3>
                            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
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
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
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
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
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
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
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
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </button>
                    </div>
                    <div id="performanceChart" class="w-full h-[300px]"></div>
                </div>

                <!-- Table Section -->
                <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 mb-2">Leden Table</h3>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </div>
                                <input type="text" placeholder="Search" class="pl-10 pr-4 py-1.5 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 focus:border-transparent w-64 text-slate-600 bg-transparent">
                            </div>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600 bg-slate-50 p-1.5 rounded-md self-start mt-[-8px]">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs font-semibold text-slate-800 border-b border-slate-100">
                                    <th class="py-4 px-2 w-12">ID</th>
                                    <th class="py-4 px-2">Name</th>
                                    <th class="py-4 px-2">Telefoon</th>
                                    <th class="py-4 px-2">Adress</th>
                                    <th class="py-4 px-2">Betaaling Status</th>
                                    <th class="py-4 px-2 w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2 text-slate-500 font-medium">6</td>
                                    <td class="py-4 px-2 text-slate-800 font-semibold">Jerry Mattedi</td>
                                    <td class="py-4 px-2 text-slate-600">+597 710 4823</td>
                                    <td class="py-4 px-2 text-slate-600">Commewijne,<br><span class="text-xs text-slate-400">Pronk weg</span></td>
                                    <td class="py-4 px-2 text-slate-600 font-medium">Betaald</td>
                                    <td class="py-4 px-2 text-center">
                                        <button class="text-slate-400 hover:text-slate-700">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2 text-slate-500 font-medium">7</td>
                                    <td class="py-4 px-2 text-slate-800 font-semibold">Elianora Vasilov</td>
                                    <td class="py-4 px-2 text-slate-600">+597 742 9156</td>
                                    <td class="py-4 px-2 text-slate-600">Paramaribo,<br><span class="text-xs text-slate-400">Kawikiweg</span></td>
                                    <td class="py-4 px-2 text-slate-600 font-medium">Niet Betaald</td>
                                    <td class="py-4 px-2 text-center">
                                        <button class="text-slate-400 hover:text-slate-700">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2 text-slate-500 font-medium">8</td>
                                    <td class="py-4 px-2 text-slate-800 font-semibold">Alvis Daan</td>
                                    <td class="py-4 px-2 text-slate-600">+597 873 9482</td>
                                    <td class="py-4 px-2 text-slate-600">Paramaribo,<br><span class="text-xs text-slate-400">Herck Arronstraat</span></td>
                                    <td class="py-4 px-2 text-slate-600 font-medium">Niet Betaald</td>
                                    <td class="py-4 px-2 text-center">
                                        <button class="text-slate-400 hover:text-slate-700">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2 text-slate-500 font-medium">9</td>
                                    <td class="py-4 px-2 text-slate-800 font-semibold">Lissa Shipway</td>
                                    <td class="py-4 px-2 text-slate-600">+597 812 5674</td>
                                    <td class="py-4 px-2 text-slate-600">Commewijne,<br><span class="text-xs text-slate-400">Balibaliweg</span></td>
                                    <td class="py-4 px-2 text-slate-600 font-medium">Betaald</td>
                                    <td class="py-4 px-2 text-center">
                                        <button class="text-slate-400 hover:text-slate-700">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center mt-6">
                        <div class="flex space-x-1 items-center">
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors bg-slate-50">1</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-white bg-slate-800 shadow-sm">2</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">3</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">4</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">5</button>
                            <span class="w-8 h-8 flex items-center justify-center text-sm text-slate-400">...</span>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">20</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Script for ApexCharts initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Contributie',
                    data: [44, 55, 41, 67, 22]
                }, {
                    name: 'Leden',
                    data: [13, 23, 20, 8, 13]
                }, {
                    name: 'Betaald',
                    data: [11, 17, 15, 15, 21]
                }, {
                    name: 'Niet betaald',
                    data: [21, 7, 25, 13, 22]
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: false,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 2
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        show: false
                    }
                },
                grid: {
                    show: false,
                },
                fill: {
                    opacity: 1
                },
                colors: ['#1e293b', '#64748b', '#94a3b8', '#cbd5e1'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                    markers: {
                        radius: 12,
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 0
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
            chart.render();
        });
    </script>
</body>
</html>