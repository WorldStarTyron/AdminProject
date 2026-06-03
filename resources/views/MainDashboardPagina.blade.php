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
                            <div class="text-[11px] text-indigo-300/80 font-semibold mb-0.5">Srd</div>
                            <div class="text-3xl font-bold text-white tracking-tight leading-none mb-3">{{number_format($totaleInkomsten, 2)}}</div>
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

                <!-- Chart and Upcoming Payments Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Monthly Performance Chart (takes 2 cols) -->
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200/80 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">

                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-base font-semibold text-slate-800">Maandelijke performance</h3>
                            <div class="flex items-center gap-3">
                                {{-- Jaar dropdown --}}
                                <select id="filterJaar" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-gray-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all cursor-pointer">
                                    <option value="">Alle jaren</option>
                                </select>

                                {{-- Maand dropdown --}}
                                <select id="filterMaand" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-gray-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all cursor-pointer">
                                    <option value="">Alle maanden</option>
                                </select>
                            </div>
                        </div>

                        <!--hier komt mijn grafiek-->
                        <div id="performanceChart" class="w-full h-[300px]"></div>

                    </div>

                    <!-- Right: Upcoming Payments (takes 1 col) -->
                    <div class="bg-white rounded-xl border border-gray-200/80 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 mb-5">Aankomende Betalingen</h3>
                            
                            <div class="space-y-3.5">
                                @forelse($deadlineLeden as $lid)
                                    @php
                                        // Retrieve the first unpaid payment to determine the deadline
                                        $firstPayment = $lid->betalingen->first();
                                        if ($firstPayment) {
                                            // The payment deadline is 1 month after the submission date (ingediend_op)
                                            $deadline = \Carbon\Carbon::parse($firstPayment->ingediend_op)->addMonth();
                                            
                                            // Calculate the signed number of days left until the deadline day
                                            $daysLeft = now()->startOfDay()->diffInDays($deadline->startOfDay(), false);
                                            
                                            // Determine urgency label and color coding based on days remaining
                                            
                                            if ($daysLeft < 0) {
                                                // Overdue: Show how many days overdue in red
                                                $daysText = 'Verlopen (' . abs($daysLeft) . ' ' . (abs($daysLeft) == 1 ? 'dag' : 'dagen') . ')';
                                                $colorClass = 'red';
                                            } elseif ($daysLeft == 0) {
                                                // Due today: High urgency in red
                                                $daysText = 'Vandaag';
                                                $colorClass = 'red';
                                            } elseif ($daysLeft == 1) {
                                                // Due tomorrow: High urgency in amber
                                                $daysText = 'Morgen';
                                                $colorClass = 'amber';
                                            } elseif ($daysLeft <= 3) {
                                                // Due within 3 days: Medium urgency in amber
                                                $daysText = 'Binnen ' . $daysLeft . ' dagen';
                                                $colorClass = 'amber';
                                            } elseif ($daysLeft <= 5) {
                                                // Due within 5 days: Moderate urgency in sky blue
                                                $daysText = 'Binnen ' . $daysLeft . ' dagen';
                                                $colorClass = 'sky';
                                            } else {
                                                // Due in 6 or 7 days: Low urgency in violet
                                                $daysText = 'Binnen ' . $daysLeft . ' dagen';
                                                $colorClass = 'violet';
                                            }
                                            
                                            $formattedDeadline = $deadline->format('d-m-Y');
                                        } else {
                                            // Fallback if no unpaid payments are found for the member
                                            $daysText = 'Geen openstaande betaling';
                                            $colorClass = 'gray';
                                            $formattedDeadline = '';
                                        }

                                        // Default fallback Tailwind styling classes (gray styling)
                                        $stripeColor = 'bg-gray-500';
                                        $iconBg = 'bg-gray-50';
                                        $iconBorder = 'border-gray-100';
                                        $iconText = 'text-gray-600';
                                        $daysTextColor = 'text-gray-600';

                                        // Map the selected urgency color class to specific Tailwind classes
                                        if ($colorClass === 'red') {
                                            $stripeColor = 'bg-red-500';
                                            $iconBg = 'bg-red-50';
                                            $iconBorder = 'border-red-100';
                                            $iconText = 'text-red-600';
                                            $daysTextColor = 'text-red-600';
                                        } elseif ($colorClass === 'amber') {
                                            $stripeColor = 'bg-amber-500';
                                            $iconBg = 'bg-amber-50';
                                            $iconBorder = 'border-amber-100';
                                            $iconText = 'text-amber-600';
                                            $daysTextColor = 'text-amber-600';
                                        } elseif ($colorClass === 'sky') {
                                            $stripeColor = 'bg-sky-500';
                                            $iconBg = 'bg-sky-50';
                                            $iconBorder = 'border-sky-100';
                                            $iconText = 'text-sky-600';
                                            $daysTextColor = 'text-sky-600';
                                        } elseif ($colorClass === 'violet') {
                                            $stripeColor = 'bg-violet-500';
                                            $iconBg = 'bg-violet-50';
                                            $iconBorder = 'border-violet-100';
                                            $iconText = 'text-violet-600';
                                            $daysTextColor = 'text-violet-600';
                                        }
                                    @endphp

                                    <div class="flex items-center justify-between p-3.5 bg-gray-50/50 border border-gray-100 rounded-xl relative overflow-hidden pl-5 hover:shadow-sm transition-all duration-200">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $stripeColor }}"></div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full {{ $iconBg }} {{ $iconBorder }} flex items-center justify-center {{ $iconText }} shadow-sm">
                                                <i class="fa-regular fa-clock text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-800">{{ $lid->gebruiker->naam }}</div>
                                                <div class="text-xs {{ $daysTextColor }} font-semibold mt-0.5" title="Deadline: {{ $formattedDeadline }}">
                                                    {{ $daysText }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-sm font-bold text-gray-900">
                                            € {{ number_format($lid->betalingen->sum('bedrag'), 2, ',', '.') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center p-6 border border-dashed border-gray-200 rounded-xl bg-gray-50/30 text-center">
                                        <div class="w-10 h-10 rounded-full bg-green-50 border border-green-100 flex items-center justify-center text-green-600 mb-3 shadow-sm">
                                            <i class="fa-regular fa-circle-check text-base"></i>
                                        </div>
                                        <div class="text-sm font-medium text-gray-700">Geen aankomende deadlines</div>
                                        <div class="text-xs text-gray-500 mt-1">Alle openstaande betalingen zijn up-to-date.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                @include('layouts.Main_Dashboard Layouts.Main_Table')
            </main>
        </div>
    </div>

    <!-- Script -->
    @vite('resources/js/Charts/MainDashboard-chart.js')

</body>
</html>