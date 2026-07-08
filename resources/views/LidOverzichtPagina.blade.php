<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lid Profiel | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/UI/Sidebar.js'])
</head>

<body class="bg-[#F8F9FA] text-slate-800 font-['Inter']">

    <div class="flex min-h-screen">

        @include('Layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">

            @include('Layouts.Headers.header')

            <main class="p-6 max-w-[1500px] mx-auto space-y-6">

                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-slate-400">
                    <a href="{{ route('MainDashboardPagina') }}" class="hover:text-violet-600 transition-colors">Dashboard</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <a href="{{ route('ledenpagina') }}" class="hover:text-violet-600 transition-colors">Leden</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-700 font-semibold">Lid Profiel</span>
                </nav>

                <!-- Page Title -->
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Lid Profiel</h1>
                    <p class="text-sm text-slate-500 mt-1">Volledig overzicht van dit lid en zijn betalingen.</p>
                </div>

                <!-- Top row: 3-column grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Card 1: Profile hero (gradient) -->
                    <div class="bg-gradient-to-br from-violet-900 via-purple-900 to-fuchsia-800 text-white rounded-xl p-6 flex flex-col items-center text-center shadow-lg shadow-violet-950/20 border-0">
                        <div class="relative mb-4">
                            <div class="w-24 h-24 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center backdrop-blur-sm">
                                <i class="fa-regular fa-user text-white/80 text-4xl"></i>
                            </div>
                            <button class="absolute -bottom-2 -right-2 w-8 h-8 bg-white text-violet-700 rounded-full flex items-center justify-center hover:bg-violet-50 transition-colors shadow-md">
                                <i class="fa-solid fa-camera text-xs"></i>
                            </button>
                        </div>
                        <h2 class="text-xl font-bold text-white leading-tight">{{ $lid->naam }}</h2>

                        <span class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-violet-100 text-xs font-semibold">
                            <i class="fa-solid fa-id-badge text-[10px]"></i>
                            Lid ID #{{ $lid->lid_nummer ?? Str::limit($lid->lid_id, 4, '') }}
                        </span>

                        @php
                            $isActief = $lid->gebruiker->status === 'Actief';
                        @endphp
                        <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider uppercase
                            {{ $isActief ? 'bg-emerald-500/20 text-emerald-200 ring-1 ring-emerald-300/30' : 'bg-amber-500/20 text-amber-200 ring-1 ring-amber-300/30' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isActief ? 'bg-emerald-300' : 'bg-amber-300' }}"></span>
                            {{ $isActief ? 'Actief' : 'Inactief' }}
                        </span>
                    </div>

                    <!-- Card 2: Member personal information -->
                    <div class="bg-white rounded-xl border border-gray-200/80 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-slate-800 mb-5">
                            <span class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                                <i class="fa-solid fa-circle-info text-xs"></i>
                            </span>
                            Informatie
                        </h3>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Email</p>
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $lid->email ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Leeftijd</p>
                                <p class="text-sm font-medium text-slate-800">
                                    @if($lid->geboortedatum)
                                        {{ \Carbon\Carbon::parse($lid->geboortedatum)->age }} jaar
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Geboortedatum</p>
                                <p class="text-sm font-medium text-slate-800">
                                    @if($lid->geboortedatum)
                                        {{ \Carbon\Carbon::parse($lid->geboortedatum)->translatedFormat('j F Y') }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Lid Type</p>
                                <p class="text-sm font-medium">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-violet-50 text-violet-700 ring-1 ring-violet-200 text-xs font-semibold">
                                        {{ $lid->lid_type ?? '—' }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Lid Sinds</p>
                                <p class="text-sm font-medium text-slate-800">
                                    @if($lid->lid_sinds)
                                        {{ \Carbon\Carbon::parse($lid->lid_sinds)->format('d-m-Y') }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Action buttons -->
                    <div class="bg-white rounded-xl border border-gray-200/80 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-slate-800 mb-5">
                            <span class="w-7 h-7 rounded-lg bg-fuchsia-50 text-fuchsia-600 flex items-center justify-center">
                                <i class="fa-solid fa-bolt text-xs"></i>
                            </span>
                            Acties
                        </h3>
                        <div class="flex flex-col gap-2.5">

                            @can('leden-beheren')
                                <a href="{{ route('ledenpagina.edit', $lid->lid_id) }}"
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg bg-gradient-to-br from-violet-600 to-fuchsia-600 text-white text-sm font-semibold hover:from-violet-700 hover:to-fuchsia-700 transition-all shadow-sm shadow-violet-900/20">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    Bewerk Lid
                                </a>
                            @endcan

                            <!-- Heractiveer knop: alleen zichtbaar als account Inactief is -->
                            @if($lid->gebruiker->status === 'Inactief')
                                @can('leden-heractiveren')
                                    <form action="{{ route('ledenpagina.heractiveer', $lid->lid_id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Weet u zeker dat u dit lid wilt heractiveren?')"
                                                class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg border border-emerald-200 bg-emerald-50/60 text-emerald-700 text-sm font-semibold hover:bg-emerald-100 hover:border-emerald-300 transition-all">
                                            <i class="fa-solid fa-user-check text-xs"></i>
                                            Heractiveer Account
                                        </button>
                                    </form>
                                @endcan
                            @endif

                            <!-- Deactiveer knop: alleen zichtbaar als account Actief is -->
                            @if($lid->gebruiker->status === 'Actief')
                                @can('leden-Deactiveren')
                                    <form action="{{ route('ledenpagina.deactiveer', $lid->lid_id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Weet u zeker dat u dit lid wilt deactiveren?')"
                                                class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg border border-amber-200 bg-amber-50/60 text-amber-700 text-sm font-semibold hover:bg-amber-100 hover:border-amber-300 transition-all">
                                            <i class="fa-solid fa-user-slash text-xs"></i>
                                            Deactiveer Account
                                        </button>
                                    </form>
                                @endcan
                            @endif

                        </div>
                    </div>
                </div>

                <!-- Bottom row: 4-column grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                    <!-- Card 4: Contact details -->
                    <div class="bg-white rounded-xl border border-gray-200/80 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-slate-800 mb-5">
                            <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i class="fa-regular fa-address-card text-xs"></i>
                            </span>
                            Contact
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-location-dot text-xs"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-0.5">Adres</p>
                                    <p class="text-sm font-medium text-slate-800 break-words">{{ $lid->adres ?? '—' }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-city text-xs"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-0.5">Woonplaats</p>
                                    <p class="text-sm font-medium text-slate-800">{{ $lid->woonplaats ?? '—' }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-0.5">Telefoon</p>
                                    <p class="text-sm font-medium text-slate-800">{{ $lid->telefoonnummer ?? '—' }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Card 5: Payment history table (spans 3 columns) -->
                    <div class="bg-white rounded-xl border border-gray-200/80 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.02)] md:col-span-3">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="flex items-center gap-2 text-base font-semibold text-slate-800">
                                <span class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                                    <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                </span>
                                Geschiedenis Betalingen
                            </h3>
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-bars-staggered text-sm"></i>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider pb-3 pr-4">ID</th>
                                        <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider pb-3 pr-4">Datum</th>
                                        <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider pb-3 pr-4">Contributiebedrag</th>
                                        <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider pb-3 pr-4">Betaling</th>
                                        <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider pb-3 pr-4">Status</th>
                                        <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider pb-3 pr-4">Month/Jaar</th>
                                        <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider pb-3">Bonnummer</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($betalingen as $betaling)
                                        <tr class="hover:bg-violet-50/30 transition-colors">
                                            <td class="py-3 pr-4 text-slate-500 font-semibold">{{ $loop->iteration }}</td>
                                            <td class="py-3 pr-4 text-slate-700">
                                                {{ \Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('j F Y') }}
                                            </td>
                                            <td class="py-3 pr-4 text-slate-700 font-mono text-xs">
                                                SRD {{ number_format($betaling->bedrag, 0) }}
                                            </td>
                                            <td class="py-3 pr-4 text-slate-900 font-semibold font-mono text-xs">
                                                SRD {{ number_format($betaling->bedrag, 0) }}
                                            </td>
                                            <td class="py-3 pr-4">
                                                @php
                                                    $statusStyles = match($betaling->status) {
                                                        'betaald'      => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
                                                        'goed_gekeurd' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
                                                        'niet_betaald' => 'bg-rose-50 text-rose-600 ring-1 ring-rose-200',
                                                        'Openstaand'   => 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200',
                                                        default        => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
                                                    };
                                                    $statusLabel = match($betaling->status) {
                                                        'betaald'      => 'Betaald',
                                                        'goed_gekeurd' => 'Goedgekeurd',
                                                        'niet_betaald' => 'Niet betaald',
                                                        'Openstaand'   => 'Openstaand',
                                                        default        => ucfirst($betaling->status),
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $statusStyles }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>

                                            <td class="py-3 text-slate-500 text-xs font-mono">
                                                @if($betaling->maand && $betaling->jaar)
                                                    {{ \Carbon\Carbon::createFromDate( $betaling->jaar, $betaling->maand,1)->translatedFormat('F Y') }}
                                                @else
                                                    —
                                                @endif
                                            </td>

                                            <td class="py-3 text-slate-500 text-xs font-mono">
                                                @if($betaling->bon)
                                                    {{ $betaling->bon->bon_nummer }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                                <i class="fa-regular fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                                                Geen betalingen gevonden voor dit lid.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($betalingen->hasPages())
                            <div class="flex items-center justify-center gap-1 mt-5 pt-4 border-t border-slate-100">

                                @if($betalingen->onFirstPage())
                                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 text-sm cursor-not-allowed">
                                        <i class="fa-solid fa-chevron-left text-xs"></i>
                                    </span>
                                @else
                                    <a href="{{ $betalingen->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-violet-50 hover:text-violet-700 transition-colors text-sm">
                                        <i class="fa-solid fa-chevron-left text-xs"></i>
                                    </a>
                                @endif

                                @foreach($betalingen->getUrlRange(1, $betalingen->lastPage()) as $page => $url)
                                    @if($page == $betalingen->currentPage())
                                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-violet-600 to-fuchsia-600 text-white text-sm font-bold shadow-sm shadow-violet-900/20">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-violet-50 hover:text-violet-700 transition-colors text-sm font-medium">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($betalingen->hasMorePages())
                                    <a href="{{ $betalingen->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-violet-50 hover:text-violet-700 transition-colors text-sm">
                                        <i class="fa-solid fa-chevron-right text-xs"></i>
                                    </a>
                                @else
                                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 text-sm cursor-not-allowed">
                                        <i class="fa-solid fa-chevron-right text-xs"></i>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Editpagina.js -->
    @vite('resources/js/Pages/EditPagina.js')
</body>
</html>
