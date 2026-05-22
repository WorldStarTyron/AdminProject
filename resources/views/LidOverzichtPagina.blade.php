<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lid Profiel | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/app.js'])
</head>


<body class="font-sans bg-gray-50 text-gray-800">
    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.header')

        <div class="p-6 max-w-7xl mx-auto">

            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <a href="{{ route('ledenpagina') }}" class="hover:text-gray-600 transition-colors">Members</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-gray-700 font-medium">Lid Profiel</span>
            </nav>

            <!-- TOP ROW -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

                <!-- Profile Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center text-center">
                    <!-- Avatar -->
                    <div class="relative mb-4">
                        <div class="w-24 h-24 rounded-2xl bg-gray-100 flex items-center justify-center">
                            <i class="fa-regular fa-user text-gray-400 text-4xl"></i>
                        </div>
                        <!-- Camera badge -->
                        <button class="absolute -bottom-2 -right-2 w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors shadow">
                            <i class="fa-solid fa-camera text-white text-xs"></i>
                        </button>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $lid->naam }}</h2>

                    <!-- Lid ID badge -->
                    <span class="mt-2 inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-medium">
                        Lid ID: #{{ $lid->lid_nummer ?? Str::limit($lid->lid_id, 4, '') }}
                    </span>
                </div>

                <!-- Information Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-5">
                        <i class="fa-solid fa-circle-info text-gray-400"></i>
                        Informatie:
                    </h3>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Email</p>
                            <p class="text-sm font-medium text-gray-800">{{ $lid->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Leeftijd</p>
                            <p class="text-sm font-medium text-gray-800">
                                @if($lid->geboortedatum)
                                    {{ \Carbon\Carbon::parse($lid->geboortedatum)->age }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Geboortedatum</p>
                            <p class="text-sm font-medium text-gray-800">
                                @if($lid->geboortedatum)
                                    {{ \Carbon\Carbon::parse($lid->geboortedatum)->translatedFormat('j F Y') }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Lid_Type</p>
                            <p class="text-sm font-medium text-gray-800">{{ $lid->lid_type ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Lid Sinds</p>
                            <p class="text-sm font-medium text-gray-800">
                                @if($lid->lid_sinds)
                                    {{ \Carbon\Carbon::parse($lid->lid_sinds)->format('d-m-Y') }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-5">
                        Action:
                    </h3> 

                    <div class="flex flex-col gap-3">
                        <!-- Update button -->
                        <a href="{{ route('ledenpagina.edit', $lid->lid_id) }}"
                           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm">
                            <i class="fa-solid fa-pen-to-square text-gray-500"></i>
                            Update
                        </a>

                        <!-- Delete button -->
                        <form action="{{ route('ledenpagina.delete', $lid->lid_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Weet u zeker dat u dit lid wilt verwijderen?')"
                                    class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl border border-red-200 bg-white text-red-500 text-sm font-medium hover:bg-red-50 hover:border-red-300 transition-all shadow-sm">
                                <i class="fa-regular fa-trash-can text-red-400"></i>
                                Verwijder
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ── BOTTOM ROW -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Contact Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-5">
                        <i class="fa-regular fa-address-card text-gray-400"></i>
                        Contact:
                    </h3>

                    <ul class="space-y-4">
                        <li>
                            <p class="text-xs text-gray-400 mb-0.5">Adres</p>
                            <p class="text-sm font-medium text-gray-800">{{ $lid->adres ?? '—' }}</p>
                        </li>
                        <li>
                            <p class="text-xs text-gray-400 mb-0.5">Woonplaats</p>
                            <p class="text-sm font-medium text-gray-800">{{ $lid->woonplaats ?? '—' }}</p>
                        </li>
                        <li>
                            <p class="text-xs text-gray-400 mb-0.5">Telefoon</p>
                            <p class="text-sm font-medium text-gray-800">{{ $lid->telefoonnummer ?? '—' }}</p>
                        </li>
                    </ul>
                </div>

                <!-- Payment History Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:col-span-3">

                    <!-- Card header -->
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                            <i class="fa-solid fa-clock-rotate-left text-gray-400"></i>
                            Geschiedenis Betaling
                        </h3>
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-bars-staggered"></i>
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3 pr-4">ID</th>
                                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3 pr-4">Datum</th>
                                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3 pr-4">Contributiebedrag</th>
                                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3 pr-4">Betaling</th>
                                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3 pr-4">Status</th>
                                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider pb-3">Bonnummer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($betalingen as $betaling)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="py-3 pr-4 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                                        <td class="py-3 pr-4 text-gray-700">
                                            {{ \Carbon\Carbon::parse($betaling->betalingsdatum)->translatedFormat('j F Y') }}
                                        </td>
                                        <td class="py-3 pr-4 text-gray-700">
                                            Srd {{ number_format($betaling->bedrag, 0) }}
                                        </td>
                                        <td class="py-3 pr-4 text-gray-700 font-medium">
                                            SRD {{ number_format($betaling->bedrag, 0) }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            @php
                                                $statusStyles = match($betaling->status) {
                                                    'Betaald'     => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200',
                                                    'Niet Betaald'=> 'bg-red-50 text-red-500 ring-1 ring-red-200',
                                                    default       => 'bg-amber-50 text-amber-600 ring-1 ring-amber-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusStyles }}">
                                                {{ $betaling->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-gray-500 text-xs font-mono">
                                            @if($betaling->bewijs_bestand)
                                                BON-KA-{{ $betaling->jaar }}-{{ str_pad($loop->iteration, 4, '0', STR_PAD_LEFT) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-gray-400 text-sm">
                                            <i class="fa-regular fa-folder-open text-2xl mb-2 block"></i>
                                            Geen betalingen gevonden voor dit lid.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($betalingen->hasPages())
                        <div class="flex items-center justify-center gap-1 mt-5 pt-4 border-t border-gray-100">
                            {{-- Previous --}}
                            @if($betalingen->onFirstPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 text-sm cursor-not-allowed">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </span>
                            @else
                                <a href="{{ $betalingen->previousPageUrl() }}"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 transition-colors text-sm">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </a>
                            @endif

                            <!-- Page numbers-->
                            @foreach($betalingen->links()->elements as $element)
                                @if(is_string($element))
                                    <span class="w-8 h-8 flex items-center justify-center text-gray-400 text-sm">{{ $element }}</span>
                                @endif
                                @if(is_array($element))
                                    @foreach($element as $page => $url)
                                        @if($page == $betalingen->currentPage())
                                            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-900 text-white text-sm font-semibold">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}"
                                               class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 transition-colors text-sm">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if($betalingen->hasMorePages())
                                <a href="{{ $betalingen->nextPageUrl() }}"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 transition-colors text-sm">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>
                            @else
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 text-sm cursor-not-allowed">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </span>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

        </div>{{-- end max-w container --}}
    </div>{{-- end main-content --}}
    @vite('resources/js/EditPagina.js')
</body>
</html>