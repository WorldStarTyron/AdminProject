<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betalingsbewijzen Beoordelen | Administratie Panel</title>
    <!-- Tailwind CSS + App JS via Vite -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/UI/Sidebar.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-[#f0f4f8] text-slate-700 font-['Inter',sans-serif] antialiased">

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        @include('Layouts.Sidebars.sidebar')

        {{-- Main Content Container --}}
        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            
            {{-- Header --}}
            @include('Layouts.Headers.header')

            {{-- Main Wrapper --}}
            <div class="max-w-6xl mx-auto px-6 py-10">

                {{-- Status messages --}}
                @if(session('success'))
                    <div class="mb-6 bg-emerald-100 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm animate-fade-in">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-rose-100 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm animate-fade-in">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- ============================================================
                     TOP SECTION: Page title + Pending Reviews badge
                ============================================================ -->
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 mb-8">
                    <!-- Page Title & Subtitle -->
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Betalingsbewijzen Beoordelen</h1>
                        <p class="text-sm text-gray-500 mt-1">Beheer en valideer ingediende documenten van leden.</p>
                    </div>

                    <!-- Pending Reviews Badge Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-5 py-4 flex items-center gap-4 min-w-[200px]">
                        <div class="bg-gray-100 rounded-lg p-2">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2h-2M9 4a2 2 0 012-2h2a2 2 0 012 2v0a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pending Reviews</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">
                                {{ $pendingCount }}
                                @if($pendingCount > 0)
                                    <span class="text-sm font-semibold text-red-500">! Actie vereist</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     MAIN TABLE CARD
                ============================================================ -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <!-- Table Header -->
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    <th class="px-6 py-3 text-left">Member Name</th>
                                    <th class="px-6 py-3 text-left">Lid ID</th>
                                    <th class="px-6 py-3 text-left">Upload Date</th>
                                    <th class="px-6 py-3 text-left">Bedrag (SRD)</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-right">Acties</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($pendingPayments as $payment)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <!-- Member Name with Avatar -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600">
                                                    {{ strtoupper(substr($payment->lid->gebruiker->naam ?? 'M', 0, 1)) }}
                                                </div>
                                                <span class="font-semibold text-gray-900">{{ $payment->lid->gebruiker->naam ?? 'Onbekend' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">#{{ $payment->lid->lid_id }}</td>
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ $payment->ingediend_op ? \Carbon\Carbon::parse($payment->ingediend_op)->translatedFormat('d M Y') : '—' }}
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-900">
                                            SRD {{ number_format($payment->bedrag, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-blue-100 text-blue-600 text-xs font-semibold px-3 py-1 rounded-full">
                                                In afwachting
                                            </span>
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('DownloadBewijsFile', $payment->betaling_id) }}" 
                                                   class="bg-gray-900 hover:bg-gray-700 text-white text-xs font-semibold px-4 py-1.5 rounded-lg transition-colors inline-block text-center">
                                                    Download
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <i class="fa-solid fa-folder-open text-2xl text-gray-300"></i>
                                                <p class="font-medium text-sm">Geen openstaande betalingsbewijzen te beoordelen.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer: Pagination -->
                    @if($pendingPayments->total() > 0)
                        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <p class="text-xs text-gray-400">
                                Toon {{ $pendingPayments->firstItem() }} t/m {{ $pendingPayments->lastItem() }} van {{ $pendingPayments->total() }} resultaten
                            </p>
                            <div class="flex items-center gap-1">
                                {{ $pendingPayments->links() }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ============================================================
                     BOTTOM SECTION: Two side-by-side cards
                ============================================================ -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Recent Beoordeeld Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="font-bold text-gray-900 text-base">Recent Beoordeeld</h2>
                            <span class="text-xs font-semibold text-gray-400">Laatste updates</span>
                        </div>

                        <ul class="space-y-4">
                            @forelse($recentReviews as $review)
                                <li class="flex items-start gap-3">
                                    @if($review->status === 'goed_gekeurd' || $review->status === 'betaald')
                                        <div class="mt-0.5 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $review->lid->gebruiker->naam ?? 'Onbekend' }} (#{{ $review->lid->lid_id }})
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                SRD {{ number_format($review->bedrag, 2, ',', '.') }} • Goedgekeurd
                                            </p>
                                        </div>
                                    @else
                                        <div class="mt-0.5 w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $review->lid->gebruiker->naam ?? 'Onbekend' }} (#{{ $review->lid->lid_id }})
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                SRD {{ number_format($review->bedrag, 2, ',', '.') }} • Afgewezen
                                            </p>
                                        </div>
                                    @endif
                                    <span class="text-xs text-gray-400 whitespace-nowrap">
                                        {{ $review->bijgewerkt_op ? \Carbon\Carbon::parse($review->bijgewerkt_op)->diffForHumans() : '—' }}
                                    </span>
                                </li>
                            @empty
                                <li class="text-center text-xs text-gray-400 py-4">
                                    Nog geen recent beoordeelde betalingen.
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Efficiëntie Report Card -->
                    <div class="bg-[#0f172a] rounded-xl shadow-sm p-6 text-white flex flex-col justify-between relative overflow-hidden">
                        {{-- Background glow --}}
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/10 rounded-full blur-xl pointer-events-none"></div>

                        <div>
                            <h2 class="font-bold text-white text-base mb-2">Efficiëntie Report</h2>
                            <p class="text-sm text-slate-300 leading-relaxed">
                                Je hebt vandaag 85% van alle binnenkomende bewijzen binnen 4 uur verwerkt. Goed bezig!
                            </p>
                        </div>

                        <div class="flex items-end justify-between mt-6">
                            <div>
                                <p class="text-4xl font-bold text-white leading-none">{{ $totalReviewedToday }}</p>
                                <p class="text-xs text-slate-400 mt-1">Totaal vandaag</p>
                            </div>
                            <svg class="w-16 h-10 text-slate-500" viewBox="0 0 64 40" fill="currentColor">
                                <rect x="0"  y="28" width="10" height="12" rx="2" class="text-slate-600" fill="currentColor"/>
                                <rect x="14" y="20" width="10" height="20" rx="2" class="text-slate-500" fill="currentColor"/>
                                <rect x="28" y="10" width="10" height="30" rx="2" class="text-slate-400" fill="currentColor"/>
                                <rect x="42" y="4"  width="10" height="36" rx="2" class="text-white"   fill="currentColor" opacity="0.8"/>
                                <rect x="56" y="16" width="8"  height="24" rx="2" class="text-slate-500" fill="currentColor"/>
                            </svg>
                        </div>
                    </div>

                </div>
                {{-- End bottom grid --}}

            </div>
            {{-- End main wrapper --}}
        </div>
    </div>

</body>
</html>