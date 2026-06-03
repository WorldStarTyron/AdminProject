<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lid profiel pagina - Bekijk lid gegevens">
    <title>Lidpagina | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/app.js'])
</head>
<body>
    <div class="flex justify-center min-h-screen">
        @include('layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('layouts.header')

            <section class="px-8 py-6">

                <!-- Profile header card -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6 mb-6">
                    <div class="flex items-start justify-between">

                        <!-- Avatar + naam + info -->
                        <div class="flex items-start gap-5">
                            <div class="relative flex-shrink-0">
                                <div class="w-[72px] h-[72px] rounded-xl overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->naam) }}&size=72&background=e2e8f0&color=475569&bold=true&font-size=0.4" alt="Profiel foto" class="w-full h-full object-cover" id="profilePhoto">
                                </div>
                                <button class="absolute -bottom-1.5 -right-1.5 w-7 h-7 bg-slate-800 hover:bg-slate-700 rounded-full flex items-center justify-center shadow transition-colors cursor-pointer" title="Foto wijzigen">
                                    <i class="fa-solid fa-camera text-white" style="font-size:10px;"></i>
                                </button>
                            </div>

                            <div class="pt-0.5">
                                <div class="flex items-center gap-2 mb-1">
                                    <h1 class="text-xl font-bold text-slate-900 m-0">{{ Auth::user()->naam }}</h1>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">ID: {{ $lid->lid_id }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">Actief</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                                        <span>Lid sinds <strong class="text-slate-700 font-semibold">{{ $lid->lid_sinds ? \Carbon\Carbon::parse($lid->lid_sinds)->format('d/m/Y') : 'Onbekend' }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-user text-slate-400 text-xs"></i>
                                        <span>{{ $lid->lid_type }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-cake-candles text-slate-400 text-xs"></i>
                                        <span>{{ $lid->geboortedatum ? \Carbon\Carbon::parse($lid->geboortedatum)->translatedFormat('d F Y') : 'Onbekend' }}
                                            @if($lid->geboortedatum)
                                                ({{ \Carbon\Carbon::parse($lid->geboortedatum)->age }} jaar)
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password reset knop rechtsboven -->
                        <a href="{{ route('recover-password') }}" class="item-center p-6" >
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-200 transition-all duration-200 shadow-sm cursor-pointer">
                                <i class="fa-solid fa-lock text-xs text-slate-500"></i>
                                Password reset
                            </button>
                        </a>
                    </div>
                </div>

                <!-- Balance + Contact Details rij -->
                <div class="flex flex-col lg:flex-row gap-6 mb-6">

                    <!-- Outstanding Balance dark card -->
                    <div class="bg-slate-900 rounded-2xl p-6 text-white lg:w-[380px] flex-shrink-0">
                        <p class="text-xs font-semibold tracking-widest text-slate-400 uppercase mb-2">Outstanding Balance</p>
                        <p class="text-4xl font-extrabold tracking-tight text-cyan-400 mb-5">
                            SRD {{ number_format($openstaandeBalans, 2, ',', '.') }}
                        </p>


                         <!-- Upcoming Deadline -->
                        <div class="flex flex-row justify-center gap-12 pt-4 border-t border-slate-700/60">
                            <div>
                                <p class="text-[10px] font-semibold tracking-widest text-slate-500 uppercase mb-1">Upcoming Deadline</p>
                                <p class="text-sm font-bold text-red-400">{{ $deadline ? $deadline->format('d M Y') : 'Geen' }}</p>
                            </div>
                            <div> 
                                <p class="text-[10px] font-semibold tracking-widest text-slate-500 uppercase mb-1">Last Payment</p>
                                <p class="text-sm font-bold text-slate-200">{{ $laatsteBetaling ? \Carbon\Carbon::parse($laatsteBetaling->ingediend_op)->translatedFormat('d M Y') : 'Geen' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold tracking-widest text-slate-500 uppercase mb-1">Upcoming Cost</p>
                                <p class="text-sm font-bold text-emerald-400">{{ $UpcomingBetaling ? 'SRD 45,00' : 'SRD 0,00' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details card -->
                    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6 flex-1 ">
                        <div class="flex items-center gap-2 mb-5">
                            <i class="fa-regular fa-address-card text-slate-500"></i>
                            <h2 class="text-base font-bold text-slate-900 m-0">Contact Details</h2>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-regular fa-envelope text-slate-500 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold tracking-widest uppercase text-slate-400 mb-0.5">Email Address</p>
                                    <p class="text-sm font-medium text-slate-800">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold tracking-widest uppercase text-slate-400 mb-0.5">Phone Number</p>
                                    
                                    <p class="text-sm font-medium text-slate-800">{{ $lid->telefoonnummer ?? 'Onbekend' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-address-book"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold tracking-widest uppercase text-slate-400 mb-0.5">Home Address</p>
                                    <p class="text-sm font-medium text-slate-800">{{ $lid->adres ?? 'Onbekend' }}</p>
                                    @if($lid->woonplaats)
                                        <p class="text-sm text-slate-500">{{ $lid->woonplaats }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Geschiedenis Betaling tabel -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <h2 class="text-base font-bold text-slate-900 m-0">Geschiedenis Betaling</h2>
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer" title="Filter">
                            <i class="fa-solid fa-filter text-slate-400 text-sm"></i>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="paymentHistoryTable">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">ID</th>
                                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Date</th>
                                    <th class="text-right text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Amount</th>
                                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Status</th>
                                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Bon nummer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($betalingen as $betaling)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 text-slate-500 font-medium">{{ $betaling->betaling_id }}</td>
                                        <td class="px-6 py-4 text-slate-600">{{ \Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('d M Y') }}</td>
                                        <td class="px-6 py-4 text-slate-800 font-semibold text-right">SRD {{ number_format($betaling->bedrag, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            @if($betaling->status === 'betaald')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">Betaald</span>
                                            @elseif($betaling->status === 'niet_betaald')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600">Niet betaald</span>
                                            @elseif($betaling->status === 'in_behandeling')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600">In behandeling</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">Afgewezen</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">{{ $betaling->bon ? $betaling->bon->bon_nummer : '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                            Geen betalingsgeschiedenis gevonden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($betalingen->hasPages())
                    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
                        <p class="text-sm text-slate-400">
                            Weergeven van {{ $betalingen->firstItem() }} tot {{ $betalingen->lastItem() }} van {{ $betalingen->total() }} betalingen
                        </p>
                        <div class="flex items-center gap-1.5">
                            {{ $betalingen->links('pagination::tailwind') }}
                        </div>
                    </div>
                    @endif
                </div>

            </section>
        </div>
    </div>
</body>
</html>