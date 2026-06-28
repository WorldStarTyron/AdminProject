<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betalingsbewijzen | Administratie Panel</title>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/UI/Sidebar.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-[#f0f4f8] text-slate-700 font-['Inter',sans-serif] antialiased">

    <!-- Outer layout: sidebar on the left, main content on the right -->
    <div class="flex min-h-screen">

        <!-- Shared sidebar navigation -->
        @include('Layouts.Sidebars.sidebar')

        <!-- Main content: offset to the right of the sidebar on desktop -->
        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">

            <!-- Shared top header bar -->
            @include('Layouts.Headers.header')

            <!-- Page content wrapper -->
            <div class="max-w-5xl mx-auto px-6 py-10">


               <!--Show after approve/reject actions or if a file error occurred. -->
                @if(session('success'))
                    <div class="mb-6 bg-emerald-100 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-rose-100 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif


                <!--
                     SECTION A — LIST PAGE (showBewijsReceived)
                     Only shown when $betaling is null (no specific payment selected).
                     This is the overview of all pending and reviewed payments. -->
                @if(!$betaling)

                    <!-- Page title for the list view -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-900">Betalingsbewijzen</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Bekijk en beoordeel ingediende betalingsbewijzen van leden.</p>
                    </div>

                    <!-- Stats row: pending count and reviewed today -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

                        <!-- Pending count card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-hourglass-half text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">In afwachting</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                            </div>
                        </div>

                        <!-- Reviewed today card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Beoordeeld vandaag</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalReviewedToday }}</p>
                            </div>
                        </div>

                    </div>

                    <!-- ── Pending payments table ── -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">

                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-hourglass-half text-gray-400 text-sm"></i>
                            <h2 class="font-semibold text-gray-900 text-sm">In afwachting van beoordeling</h2>
                        </div>

                        @if($pendingPayments->isEmpty())
                            <!-- Empty state: no pending payments -->
                            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                                <i class="fa-solid fa-inbox text-3xl mb-3 block"></i>
                                Geen bewijzen in afwachting.
                            </div>
                        @else
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold">Lid</th>
                                        <th class="px-6 py-3 text-left font-semibold">Bedrag</th>
                                        <th class="px-6 py-3 text-left font-semibold">Ingediend op</th>
                                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                                        <th class="px-6 py-3 text-right font-semibold">Actie</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($pendingPayments as $pending)
                                        <tr class="hover:bg-gray-50 transition-colors">

                                            <!-- Member name -->
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                {{ $pending->lid->gebruiker->naam ?? 'Onbekend' }}
                                                <span class="block text-xs text-gray-400 font-normal">Lid #{{ $pending->lid->lid_id ?? '—' }}</span>
                                            </td>

                                            <!-- Payment amount -->
                                            <td class="px-6 py-4 text-gray-700">
                                                SRD {{ number_format($pending->bedrag, 2, ',', '.') }}
                                            </td>

                                            <!-- Date submitted -->
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $pending->ingediend_op
                                                    ? \Carbon\Carbon::parse($pending->ingediend_op)->translatedFormat('d M Y')
                                                    : '—' }}
                                            </td>

                                            <!-- Status badge -->
                                            <td class="px-6 py-4">
                                                <span class="bg-blue-100 text-blue-600 text-xs font-semibold px-3 py-1 rounded-full">
                                                    In afwachting
                                                </span>
                                            </td>

                                            <!-- View proof link -->
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('ViewBewijsFile', $pending->betaling_id) }}"
                                                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                                    Bekijken
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination links -->
                            <div class="px-6 py-4 border-t border-gray-100">
                                {{ $pendingPayments->links() }}
                            </div>
                        @endif

                    </div>

                    <!-- ── Recent reviews table ── -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-gray-400 text-sm"></i>
                            <h2 class="font-semibold text-gray-900 text-sm">Recente beoordelingen</h2>
                        </div>

                        @if($recentReviews->isEmpty())
                            <!-- Empty state: no recent reviews -->
                            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                                <i class="fa-solid fa-inbox text-3xl mb-3 block"></i>
                                Nog geen beoordelingen.
                            </div>
                        @else
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold">Lid</th>
                                        <th class="px-6 py-3 text-left font-semibold">Bedrag</th>
                                        <th class="px-6 py-3 text-left font-semibold">Datum</th>
                                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                                        <th class="px-6 py-3 text-right font-semibold">Actie</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($recentReviews as $review)
                                        <tr class="hover:bg-gray-50 transition-colors">

                                            <!-- Member name -->
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                {{ $review->lid->gebruiker->naam ?? 'Onbekend' }}
                                                <span class="block text-xs text-gray-400 font-normal">Lid #{{ $review->lid->lid_id ?? '—' }}</span>
                                            </td>

                                            <!-- Amount -->
                                            <td class="px-6 py-4 text-gray-700">
                                                SRD {{ number_format($review->bedrag, 2, ',', '.') }}
                                            </td>

                                            <!-- Date -->
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $review->ingediend_op
                                                    ? \Carbon\Carbon::parse($review->ingediend_op)->translatedFormat('d M Y')
                                                    : '—' }}
                                            </td>

                                            <!-- Status badge: colour matches the current status -->
                                            <td class="px-6 py-4">
                                                @if($review->status === 'betaald' || $review->status === 'goed_gekeurd')
                                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                                        Goedgekeurd
                                                    </span>
                                                @elseif($review->status === 'niet_goedgekeurd' || $review->status === 'Openstaand')
                                                    <span class="bg-red-100 text-red-600 text-xs font-semibold px-3 py-1 rounded-full">
                                                        Niet goedgekeurd
                                                    </span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-3 py-1 rounded-full">
                                                        {{ $review->status }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- View proof link (only if file exists) -->
                                            <td class="px-6 py-4 text-right">
                                                @if($review->betaling_bewijs)
                                                    <a href="{{ route('ViewBewijsFile', $review->betaling_id) }}"
                                                       class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                                        Bekijken
                                                    </a>
                                                @else
                                                    <span class="text-xs text-gray-400">Geen bestand</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                    </div>

                @endif
                <!-- End Section A (list page) -->

    @include('Layouts.ViewBewijs.ViewbetalingBewijs')

</body>
</html>
