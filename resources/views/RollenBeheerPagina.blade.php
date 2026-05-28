<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rollen Toewijzen</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css'])
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen transition-all duration-300">
        @include('layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden flex flex-col">
            @include('layouts.header')
            <main class="flex-1 p-8">

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-green-500"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-user-shield text-gray-700"></i>
                            Rollen Toewijzen
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 max-w-md">
                            Beheer welke rollen zijn toegewezen aan uw leden. Zoek een gebruiker en wijzig hun toegangsrechten direct.
                        </p>
                    </div>
                </div>

                {{-- Snel Rol Toewijzen --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-yellow-500"></i>
                        Snel Rol Toewijzen
                    </h2>
                    <form action="{{ route('rollen-beheer.assign') }}" method="POST" id="quickAssignForm">
                        @csrf
                        <div class="flex flex-wrap items-end gap-3">
                            {{-- Zoek Gebruiker met autocomplete --}}
                            <div class="flex flex-col gap-1.5 flex-1 min-w-[180px] relative">
                                <label class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    Zoek Gebruiker (Lid)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                    </span>
                                    <input type="text" id="user_search" placeholder="Naam of email..." class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white text-gray-700 placeholder-gray-400" autocomplete="off">
                                    <input type="hidden" name="gebruiker_id" id="selected_user_id">
                                    <div id="autocomplete_results" class="hidden absolute bg-white border border-gray-200 max-h-[200px] overflow-y-auto z-[1000] w-full rounded-lg shadow-md top-full left-0"></div>
                                </div>
                            </div>

                            {{-- Selecteer Rol --}}
                            <div class="flex flex-col gap-1.5 flex-1 min-w-[160px]">
                                <label class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fa-solid fa-tag"></i>
                                    Selecteer Rol
                                </label>
                                <select name="rol_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white text-gray-700">
                                    <option value="">Kies een rol...</option>
                                    @foreach($alleRollen as $rol)
                                        <option value="{{ $rol->rol_id }}">{{ $rol->naam }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tijdelijk Wachtwoord --}}
                            <div class="flex flex-col gap-1.5 flex-1 min-w-[160px]">
                                <label class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fa-solid fa-key"></i>
                                    Tijdelijk Wachtwoord (optioneel)
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="tijdelijk_wachtwoord" placeholder="Wachtwoord..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white text-gray-700 placeholder-gray-400">
                                </div>
                            </div>

                            <button type="submit" class="bg-gray-900 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors whitespace-nowrap flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Toewijzing Opslaan
                            </button>
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <input type="checkbox" name="verplicht_wijzigen" id="verplicht_wachtwoord" value="1" class="w-3.5 h-3.5 rounded border-gray-300 text-gray-900 focus:ring-gray-400 cursor-pointer">
                            <label for="verplicht_wachtwoord" class="text-xs text-gray-500 cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-rotate text-gray-400"></i>
                                Verplicht wachtwoord wijzigen bij eerste inlog
                            </label>
                        </div>
                    </form>
                </div>

                {{-- Bottom section: table + tips --}}
                <div class="flex gap-5 items-start">
                    {{-- Gebruikers & Rollen tabel --}}
                    <div class="flex-1 bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <i class="fa-solid fa-users text-gray-500"></i>
                                Gebruikers & Rollen
                            </h2>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-filter"></i>
                                    Filter:
                                </span>
                                <form method="GET" action="{{ route('rollen-beheer') }}" class="flex items-center gap-2">
                                    <select name="rol_id" onchange="this.form.submit()" class="text-xs border-gray-200 rounded-lg py-1 px-2">
                                        <option value="">Alle Rollen</option>
                                        @foreach($alleRollen as $rol)
                                            <option value="{{ $rol->rol_id }}" {{ request('rol_id') == $rol->rol_id ? 'selected' : '' }}>{{ $rol->naam }}</option>
                                        @endforeach
                                    </select>
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                                        <input type="text" name="search" placeholder="Zoek gebruiker..." value="{{ request('search') }}" class="text-xs border border-gray-200 rounded-lg pl-6 pr-2 py-1 w-40">
                                    </div>
                                    <button type="submit" class="text-xs bg-gray-100 px-2 py-1 rounded flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                        Filter
                                    </button>
                                    @if(request('search') || request('rol_id'))
                                        <a href="{{ route('rollen-beheer') }}" class="text-xs text-red-500 flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i>
                                            wis
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Gebruikers & Rollen Tabel -->
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-user"></i>
                                            Gebruiker
                                        </span>
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-shield-halved"></i>
                                            Huidige Rol(len)
                                        </span>
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-regular fa-clock"></i>
                                            Laatst Gewijzigd
                                        </span>
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-sliders"></i>
                                            Acties
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($gebruikers as $gebruiker)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-semibold text-blue-600 shrink-0">
                                                {{ strtoupper(substr($gebruiker->naam, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">{{ $gebruiker->naam }}</p>
                                                <p class="text-xs text-gray-400 flex items-center gap-1">
                                                    <i class="fa-regular fa-envelope"></i>
                                                    {{ $gebruiker->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                     
                                    <!-- Huidige rol -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($gebruiker->rollen as $rol)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium 
                                                    {{ $rol->naam == 'Applicatie Beheerder' ? 'bg-gray-800 text-white' : 
                                                       ($rol->naam == 'Voorzitter' ? 'bg-gray-100 text-gray-700 border border-gray-200' : 
                                                       ($rol->naam == 'Administratie Medewerker' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 
                                                       'bg-gray-100 text-gray-700 border border-gray-200')) }}">
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
                                                <span class="text-gray-400 text-xs flex items-center gap-1">
                                                    <i class="fa-regular fa-circle-xmark"></i>
                                                    Geen rol
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-gray-500">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-regular fa-calendar text-gray-400"></i>
                                            {{ $gebruiker->bijgewerkt_op ? $gebruiker->bijgewerkt_op->format('d M Y') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <button type="button" onclick="openRoleModal({{ $gebruiker->gebruiker_id }})" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            Bewerk Rol
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                                        <i class="fa-solid fa-users-slash text-2xl mb-2 block"></i>
                                        Geen gebruikers gevonden.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <i class="fa-solid fa-list-ol"></i>
                                Toont {{ $gebruikers->firstItem() ?? 0 }} - {{ $gebruikers->lastItem() ?? 0 }} van {{ $gebruikers->total() }} gebruikers
                            </span>
                            <div class="flex items-center gap-2">
                                {{ $gebruikers->appends(request()->query())->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </div>

                    {{-- Beveiligingstips --}}
                    <div class="w-56 bg-white border border-gray-200 rounded-xl p-4 shrink-0">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fa-solid fa-shield text-gray-700"></i>
                            <h3 class="text-xs font-semibold text-gray-700">Beveiligingstips</h3>
                        </div>
                        <ul class="flex flex-col gap-3">
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-circle-check text-green-600 shrink-0 mt-0.5 text-sm"></i>
                                <p class="text-xs text-gray-500 leading-relaxed">Gebruik het principe van 'least privilege': geef gebruikers alleen de machtigingen die ze echt nodig hebben.</p>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-circle-check text-green-600 shrink-0 mt-0.5 text-sm"></i>
                                <p class="text-xs text-gray-500 leading-relaxed">Review maandelijks de lijst met actieve beheerders.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </main>
        </div>
    </div>

    {{-- Modal for editing user roles --}}
    <div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-gray-700"></i>
                Rollen bewerken
            </h3>
            <form id="roleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-gray-500"></i>
                        Rollen
                    </label>
                    <div id="roleCheckboxes" class="space-y-2">
                        {{-- Dynamic checkboxes will be injected here --}}
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2 border rounded-lg flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                        Annuleren
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Opslaan
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Rollen scripts -->
    <script>
        window.alleRollenData = @json($alleRollen);
        window.updateRolUrl   = "{{ url('/rollen-beheer/gebruiker') }}";
        window.searchUsersUrl = "{{ route('rollen-beheer.search') }}";
        window.csrfToken      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>

    @vite(['resources/js/app.js', 'resources/js/Rolbeheer.js'])
</body>
</html>