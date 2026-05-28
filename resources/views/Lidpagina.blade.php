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
    <div class="flex min-h-screen">
        @include('layouts.Sidebars.sidebar-lid')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('layouts.header')

            <section class="px-8 py-6">
                <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 p-8 animate-[fadeSlideUp_0.5s_ease-out]">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-8">
                        <div class="flex items-start gap-6 flex-1">
                            <div class="relative flex-shrink-0">
                                <div class="w-[120px] h-[120px] rounded-2xl overflow-hidden border-2 border-slate-200 shadow-sm">
                                    <img src="https://ui-avatars.com/api/?name=R+B&size=120&background=e0e7ff&color=1e3a8a&bold=true&font-size=0.4" alt="Profiel foto" class="w-full h-full object-cover" id="profilePhoto">
                                </div>
                                <button class="absolute -bottom-1.5 -right-1.5 w-8 h-8 bg-slate-800 hover:bg-slate-700 rounded-full flex items-center justify-center shadow-lg transition-colors cursor-pointer" title="Foto wijzigen">
                                    <i class="fa-solid fa-camera text-white text-xs"></i>
                                </button>
                            </div>

                            <div class="flex flex-col gap-3 pt-1">
                                <div class="flex items-center gap-3">
                                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight m-0">{{ Auth::user()->naam }}</h1>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-gradient-to-r from-cyan-500 to-teal-500 text-white text-xs font-bold tracking-wide shadow-sm">Lid ID: #{{ $lid->lid_id }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                                        <span>Lid sinds <strong class="text-slate-700 font-semibold">{{ $lid->lid_sinds ? \Carbon\Carbon::parse($lid->lid_sinds)->format('d/m/Y') : 'Onbekend' }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-user text-slate-400 text-xs"></i>
                                        <span><strong class="text-slate-700 font-semibold">{{ $lid->geboortedatum ? \Carbon\Carbon::parse($lid->geboortedatum)->translatedFormat('d F Y') : 'Onbekend' }}</strong> ({{ $lid->geboortedatum ? \Carbon\Carbon::parse($lid->geboortedatum)->age : '?' }} jaar)</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 mt-2">
                                    <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                        Password reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="hidden lg:block w-px h-32 bg-slate-200 self-center"></div>

                        <div class="w-full lg:w-auto lg:min-w-[320px]">
                            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                                <p class="text-xs font-bold tracking-[0.15em] text-slate-400 uppercase mb-3">Outstanding Balance</p>
                                <p class="text-4xl font-extrabold tracking-tight mb-5">
                                    <span class="text-lg font-bold text-slate-300 mr-1">SRD</span>{{ number_format($openstaandeBalans, 2, ',', '.') }}
                                </p>
                                <div class="space-y-2.5 pt-3 border-t border-slate-700/60">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-400">Last Payment</span>
                                        <span class="font-semibold text-slate-200">{{ $laatsteBetaling ? \Carbon\Carbon::parse($laatsteBetaling->ingediend_op)->translatedFormat('d M Y') : 'Geen' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-400">Upcoming Cost</span>
                                        <span class="font-semibold text-emerald-400">SRD 45.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
    

            <!-- Contact info -->
            <div class="px-8 py-6">
                <div class="flex flex-col lg:flex-row gap-6 w-full items-start">
                    <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 w-full lg:w-[320px] flex-shrink-0">
                        <div class="flex items-center gap-3 p-6 pb-4">
                            <i class="fa-regular fa-file text-slate-700"></i>
                            <h2 class="text-base font-bold text-slate-900">Contact Details</h2>
                        </div>
                        <div class="px-6 pb-6 space-y-5">
                            <div>
                                <p class="text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 mb-1">Email Address</p>
                                <p class="text-sm font-medium text-slate-800">{{ Auth::user()->email }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 mb-1">Telefoon Number</p>
                                <p class="text-sm font-medium text-slate-800">{{ $lid->telefoonnummer ?? 'Onbekend' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 mb-1">Address</p>
                                <p class="text-sm font-medium text-slate-800">{{ $lid->adres ?? 'Onbekend' }}</p>
                                <p class="text-sm text-slate-500">{{ $lid->woonplaats ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 flex-1 min-w-0">
                        <div class="flex items-center justify-between p-6 pb-4">
                            <div class="flex items-center gap-3">
                                <i class="fa-regular fa-clock text-slate-700"></i>
                                <h2 class="text-base font-bold text-slate-900">Payment History</h2>
                            </div>
                            <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer" title="Filter">
                                <i class="fa-solid fa-filter text-slate-500 text-sm"></i>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm" id="paymentHistoryTable">
                                <thead>
                                    <tr class="border-t border-b border-slate-100">
                                        <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">ID</th>
                                        <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">Date</th>
                                        <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">Contribution</th>
                                        <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">Status</th>
                                        <th class="text-left text-[11px] font-semibold tracking-[0.08em] uppercase text-slate-400 px-6 py-3">Bonnummer</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($betalingen as $betaling)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 text-slate-600 font-medium">{{ $betaling->betaling_id }}</td>
                                            <td class="px-6 py-4 text-slate-600">{{ \Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('d M Y') }}</td>
                                            <td class="px-6 py-4 text-slate-700 font-medium">SRD {{ number_format($betaling->bedrag, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4">
                                                @if($betaling->status === 'betaald')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-emerald-50 text-emerald-600">BETAALD</span>
                                                @elseif($betaling->status === 'niet_betaald')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-red-50 text-red-600">NIET BETAALD</span>
                                                @elseif($betaling->status === 'in_behandeling')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-amber-50 text-amber-600">IN BEHANDELING</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide bg-slate-100 text-slate-600">AFGEWEZEN</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-slate-500">{{ $betaling->bon ? $betaling->bon->bon_nummer : '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
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
                </div>
            </div>
        </div>
    </div>
</body>
</html>