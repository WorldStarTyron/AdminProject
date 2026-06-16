<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GebruikerBeheerPagina</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Stylesheets & Scripts -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/UI/Sidebar.js', 'resources/js/AddModals/AddGebruikerModal.js'])
</head>
<body class="min-h-screen bg-white font-sans antialiased">
    <!-- Main page flex wrapper -->
    <div class="flex min-h-screen transition-all duration-300">
        <!-- Sidebar inclusion -->
        @include('Layouts.Sidebars.sidebar')

        <!-- Main content area -->
        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('Layouts.Headers.AddGebruiker-Header')

            <div class="w-full h-[calc(100vh-76px)]">
                <div class="w-full h-full p-1">

                    <!-- Flash messages: succes -->
                    @if(session('success'))
                        <div class="mx-5 mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-green-500"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Flash messages: fout -->
                    @if(session('error'))
                        <div class="mx-5 mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Validatiefouten -->
                    @if($errors->any())
                        <div class="mx-5 mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Paginatitel -->
                    <div class="ml-5">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2 mt-5 mb-1">
                            <i class="fas fa-user-cog mr-1 text-gray-500"></i>
                            Gebruikerbeheer
                        </h2>
                        <p class="text-sm ml-12 text-gray-600">Hier kunt u alle geregistreerde applicatiegebruikers overzichtelijk bekijken en beheren.</p>
                    </div>

                    <!-- Stat cards -->
                    <div class="flex flex-row justify-center mt-5 gap-4 px-5">

                        <!-- Totaal Gebruikers -->
                        <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
                            <div class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-users text-lg text-blue-600"></i>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-xs text-gray-500 font-medium">Totaal Gebruikers</span>
                                <strong class="text-3xl font-bold text-gray-900">{{ $totaalGebruikers }}</strong>
                            </div>
                        </div>

                        <!-- Admins -->
                        <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
                            <div class="w-11 h-11 rounded-full bg-purple-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-user-shield text-lg text-purple-600"></i>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-xs text-gray-500 font-medium">Administratie Medewerker</span>
                                <strong class="text-3xl font-bold text-gray-900">{{ $totaalAdmins }}</strong>
                            </div>
                        </div>

                        <!-- Voorzitter -->
                        <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
                            <div class="w-11 h-11 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-user-tie text-lg text-amber-600"></i>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-xs text-gray-500 font-medium">Voorzitter</span>
                                <strong class="text-3xl font-bold text-gray-900">{{ $totaalVoorzitters }}</strong>
                            </div>
                        </div>

                    </div>

                    <!-- Zoekbalk + Filter knop -->
                    <form method="GET" action="{{ route('GebruikersBeheer') }}" id="filterForm">
                        <div class="flex flex-row items-center gap-3 w-full mt-5 px-5">
                            <div class="relative flex-1">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input
                                    type="text"
                                    name="zoek"
                                    value="{{ request('zoek') }}"
                                    class="border border-gray-200 w-full rounded-xl p-2 pl-9"
                                    placeholder="Zoek op naam, email of rol...">
                            </div>

                            <!-- Filter knop: oranje als er actieve filters zijn -->
                            <button class="px-6 py-2 rounded-full {{ request('status') || request('rol') ? 'bg-orange-600' : 'bg-orange-500' }} hover:bg-orange-600 text-white font-bold text-sm transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
                                    id="filterBtn" type="button">
                                <i class="fas fa-sliders-h"></i>
                                <span>Filters</span>
                                <!-- Toon een teller als er filters actief zijn -->
                                @if(request('status') || request('rol'))
                                    <span class="w-5 h-5 rounded-full bg-white text-orange-600 text-xs font-bold flex items-center justify-center">
                                        {{ (request('status') ? 1 : 0) + (request('rol') ? 1 : 0) }}
                                    </span>
                                @endif
                            </button>
                        </div>

                        <!-- Filter dropdown paneel: verborgen tenzij er actieve filters zijn -->
                        <div id="filterPanel" class="px-5 mt-3 {{ request('status') || request('rol') ? '' : 'hidden' }}">
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <div class="flex flex-row items-end gap-4">

                                    <!-- Status filter -->
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                                        <select name="status" id="statusFilter"
                                                class="w-full border border-gray-200 rounded-lg p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-400">
                                            <option value="">Alle statussen</option>
                                            <option value="Actief"   {{ request('status') == 'Actief'   ? 'selected' : '' }}>Actief</option>
                                            <option value="Inactief" {{ request('status') == 'Inactief' ? 'selected' : '' }}>Inactief</option>
                                        </select>
                                    </div>

                                    <!-- Rol filter -->
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Rol</label>
                                        <select name="rol" id="rolFilter"
                                                class="w-full border border-gray-200 rounded-lg p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-400">
                                            <option value="">Alle rollen</option>
                                            @foreach($rollen as $rol)
                                                <option value="{{ $rol->naam }}" {{ request('rol') == $rol->naam ? 'selected' : '' }}>
                                                    {{ $rol->naam }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Toepassen & Reset knoppen -->
                                    <div class="flex items-center gap-2">
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm transition-all duration-200">
                                            Toepassen
                                        </button>
                                        <a href="{{ route('GebruikersBeheer') }}"
                                           class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 font-semibold text-sm transition-all duration-200">
                                            Reset
                                        </a>
                                    </div>
                                </div>

                                <!-- Actieve filter tags: toon welke filters aan staan -->
                                @if(request('status') || request('rol'))
                                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-200">
                                        <span class="text-xs text-gray-500 font-medium">Actieve filters:</span>

                                        <!-- Status filter tag met verwijder knop -->
                                        @if(request('status'))
                                            <a href="{{ route('GebruikersBeheer', array_merge(request()->except('status'), ['page' => null])) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-medium hover:bg-orange-200 transition-colors">
                                                Status: {{ request('status') }}
                                                <i class="fas fa-times text-[10px]"></i>
                                            </a>
                                        @endif

                                        <!-- Rol filter tag met verwijder knop -->
                                        @if(request('rol'))
                                            <a href="{{ route('GebruikersBeheer', array_merge(request()->except('rol'), ['page' => null])) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-medium hover:bg-orange-200 transition-colors">
                                                Rol: {{ request('rol') }}
                                                <i class="fas fa-times text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Filter paneel toggle script -->
                    <script>
                        document.getElementById('filterBtn').addEventListener('click', function () {
                            const panel = document.getElementById('filterPanel');
                            panel.classList.toggle('hidden');
                        });
                    </script>

                    <!-- Gebruikerstabel -->
                    <div class="mt-5 px-5">
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 bg-gray-50">
                                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Gebruiker</th>
                                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Email</th>
                                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Rol</th>
                                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Status</th>
                                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Acties</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">

                                    @php
                                        // Beschikbare kleuren voor de avatar cirkels
                                        $avatarKleuren = [
                                            'bg-blue-100 text-blue-600',
                                            'bg-purple-100 text-purple-600',
                                            'bg-green-100 text-green-600',
                                            'bg-amber-100 text-amber-600',
                                            'bg-pink-100 text-pink-600',
                                            'bg-red-100 text-red-600',
                                        ];
                                        $avatarIndex = 0;
                                    @endphp

                                    {{--
                                        FIX: Controleer EERST of $gebruikers leeg is.
                                        Zonder deze check crasht firstItem() / lastItem() bij 0 resultaten,
                                        en bestaat $gebruiker nooit — wat de "Undefined variable" fout geeft.
                                    --}}
                                    @if($gebruikers->isEmpty())

                                        <!-- Lege staat: vriendelijke melding in plaats van een lege of crashende tabel -->
                                        <tr>
                                            <td colspan="5" class="px-6 py-16 text-center">
                                                <div class="flex flex-col items-center gap-3">

                                                    <!-- Icoon -->
                                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <i class="fas fa-user-slash text-2xl text-gray-400"></i>
                                                    </div>

                                                    {{--
                                                        $geenResultaten = true  → er is gezocht maar niets gevonden
                                                        $geenResultaten = false → er zijn überhaupt geen gebruikers
                                                    --}}
                                                    @if($geenResultaten)
                                                        <!-- De gebruiker heeft gezocht maar er zijn geen overeenkomsten -->
                                                        <p class="text-gray-700 font-semibold text-base">Geen resultaten gevonden.</p>
                                                        <p class="text-gray-400 text-sm">
                                                            Er zijn geen gebruikers die overeenkomen met
                                                            @if(request('zoek'))
                                                                <!-- Toon de ingevoerde zoekterm terug aan de gebruiker -->
                                                                "<span class="font-medium text-gray-600">{{ request('zoek') }}</span>"
                                                            @else
                                                                de geselecteerde filters
                                                            @endif
                                                        </p>
                                                        <!-- Filters wissen knop zodat de gebruiker makkelijk terug kan -->
                                                        <a href="{{ route('GebruikersBeheer') }}"
                                                           class="mt-2 px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 font-semibold text-sm transition-all duration-200">
                                                            <i class="fas fa-rotate-left mr-1"></i>
                                                            Filters wissen
                                                        </a>
                                                    @else
                                                        <!-- Er zijn nog geen gebruikers aangemaakt in het systeem -->
                                                        <p class="text-gray-700 font-semibold text-base">Geen gebruikers gevonden.</p>
                                                        <p class="text-gray-400 text-sm">Er zijn nog geen gebruikers aangemaakt in het systeem.</p>
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>

                                    @else

                                        @foreach($gebruikers as $gebruiker)
                                            @php
                                                // Kies de volgende avatarkleur voor deze gebruiker
                                                $avatarKleur = $avatarKleuren[$avatarIndex % count($avatarKleuren)];
                                                $avatarIndex++;

                                                // Bevestigingsteksten voor de deactiveer- en heractiveer-knoppen
                                                $bevestigDeactiveer  = "Weet je zeker dat je {$gebruiker->naam} wilt deactiveren?";
                                                $bevestigHeractiveer = "Weet je zeker dat je {$gebruiker->naam} wilt heractiveren?";
                                            @endphp

                                            <tr id="user-row-{{ $gebruiker->gebruiker_id }}" class="hover:bg-gray-50 transition-colors duration-150">

                                                <!-- Gebruiker: avatar + naam + ID -->
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <!-- Avatar cirkel met initialen -->
                                                        <div class="w-9 h-9 rounded-full {{ $avatarKleur }} flex items-center justify-center shrink-0 text-sm font-semibold">
                                                            {{ strtoupper(substr($gebruiker->naam, 0, 2)) }}
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="font-semibold text-gray-900 cell-naam">{{ $gebruiker->naam }}</span>
                                                            <span class="text-xs text-gray-400">ID: #{{ $gebruiker->gebruiker_id }}</span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Email -->
                                                <td class="px-6 py-4 text-gray-600 cell-email">{{ $gebruiker->email }}</td>

                                                <!-- Rol badge(s) -->
                                                <td class="px-6 py-4">
                                                    @forelse($gebruiker->rollen as $rol)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                                            {{ $rol->naam == 'Applicatie Beheerder'      ? 'bg-red-800 text-white' :
                                                               ($rol->naam == 'Voorzitter'               ? 'bg-amber-100 text-amber-700 border border-amber-200' :
                                                               ($rol->naam == 'Administratie Medewerker' ? 'bg-purple-100 text-purple-700 border border-purple-200' :
                                                               'bg-gray-100 text-gray-600 border border-gray-200')) }}">
                                                            <!-- Rol icoon op basis van rolnaam -->
                                                            @if($rol->naam == 'Applicatie Beheerder')
                                                                <i class="fa-solid fa-crown text-[10px]"></i>
                                                            @elseif($rol->naam == 'Voorzitter')
                                                                <i class="fa-solid fa-star text-[10px]"></i>
                                                            @elseif($rol->naam == 'Administratie Medewerker')
                                                                <i class="fa-solid fa-briefcase text-[10px]"></i>
                                                            @else
                                                                <i class="fa-solid fa-circle-user text-[10px]"></i>
                                                            @endif
                                                            {{ $rol->naam }}
                                                        </span>
                                                    @empty
                                                        <!-- Gebruiker heeft geen rol toegewezen gekregen -->
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-500 border border-red-100">
                                                            <i class="fa-regular fa-circle-xmark text-[10px]"></i>
                                                            Geen rol
                                                        </span>
                                                    @endforelse
                                                </td>

                                                <!-- Status badge -->
                                                <td class="px-6 py-4 cell-status">
                                                    @if($gebruiker->status === 'Actief')
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                                            Actief
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                                            Inactief
                                                        </span>
                                                    @endif
                                                </td>

                                                <!-- Acties: bewerken, deactiveren of heractiveren -->
                                                <td class="flex gap-3 px-6 py-4">

                                                    <!-- Edit knop: opent de modal en vult de gegevens in -->
                                                    <button
                                                        onclick="openModal()"
                                                        class="openEditGebruikerModalBtn text-green-600 hover:text-green-900 cursor-pointer"
                                                        data-gebruiker-id="{{ $gebruiker->gebruiker_id }}"
                                                        data-naam="{{ $gebruiker->naam }}"
                                                        data-email="{{ $gebruiker->email }}"
                                                        data-rol="{{ $gebruiker->rollen->pluck('naam')->join(', ') }}"
                                                        data-status="{{ $gebruiker->status }}"
                                                        data-aangemaakt-op="{{ $gebruiker->aangemaakt_op }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                        
                                                    @if($gebruiker->status === 'Actief')
                                                        <!-- Deactiveer knop: stuurt PUT request om gebruiker te deactiveren -->
                                                        <form
                                                            method="POST"
                                                            action="{{ route('GebruikersBeheer.deactiveer', $gebruiker->gebruiker_id) }}"
                                                            onsubmit="return confirm('{{ $bevestigDeactiveer }}')">
                                                            @csrf
                                                            @method('PUT')
                                                            <button
                                                                type="submit"
                                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-red-500 hover:bg-red-600 text-sm font-medium transition-colors duration-150"
                                                                title="Deactiveer gebruiker">
                                                                <i class="fas fa-toggle-off"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <!-- Heractiveer knop: stuurt PUT request om gebruiker te heractiveren -->
                                                        <form
                                                            method="POST"
                                                            action="{{ route('GebruikersBeheer.heractiveer', $gebruiker->gebruiker_id) }}"
                                                            onsubmit="return confirm('{{ $bevestigHeractiveer }}')">
                                                            @csrf
                                                            @method('PUT')
                                                            <button
                                                                type="submit"
                                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-green-500 hover:bg-green-600 text-sm font-medium transition-colors duration-150"
                                                                title="Heractiveer gebruiker">
                                                                <i class="fas fa-toggle-on"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach {{-- Einde van de gebruikers loop --}}

                                    @endif {{-- Einde van de isEmpty check --}}

                                </tbody>
                            </table>

                            {{--
                                FIX: Paginering alleen tonen als er resultaten zijn.
                                firstItem() en lastItem() geven NULL terug bij een lege collectie
                                en veroorzaken een PHP Notice / crash.
                                Door @if($gebruikers->isNotEmpty()) te checken is dit nu veilig.
                            --}}
                            @if($gebruikers->isNotEmpty())
                                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
                                    <span class="text-sm text-gray-500">
                                        <!-- firstItem() en lastItem() zijn nu veilig omdat we weten dat er resultaten zijn -->
                                        Toon {{ $gebruikers->firstItem() }} tot {{ $gebruikers->lastItem() }} van {{ $gebruikers->total() }} gebruikers
                                    </span>
                                    <div class="flex items-center gap-1">
                                        <!-- Vorige pagina knop -->
                                        <a href="{{ $gebruikers->previousPageUrl() }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors duration-150 {{ $gebruikers->onFirstPage() ? 'pointer-events-none opacity-40' : '' }}">
                                            <i class="fas fa-chevron-left text-xs"></i>
                                        </a>

                                        <!-- Paginanummers -->
                                        @foreach($gebruikers->getUrlRange(1, $gebruikers->lastPage()) as $page => $url)
                                            <a href="{{ $url }}"
                                               class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors duration-150
                                                      {{ $page == $gebruikers->currentPage()
                                                            ? 'bg-gray-900 text-white'
                                                            : 'border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                                {{ $page }}
                                            </a>
                                        @endforeach

                                        <!-- Volgende pagina knop -->
                                        <a href="{{ $gebruikers->nextPageUrl() }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors duration-150 {{ !$gebruikers->hasMorePages() ? 'pointer-events-none opacity-40' : '' }}">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif {{-- Einde paginering check --}}

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div> <!-- Sluit de flex min-h-screen wrapper -->

    <!-- Modals -->
    @include('Layouts.EditModals.edit-Gebruiker-modal')
    @include('Layouts.AddModals.AddGebruikerModal')

</body>
</html>