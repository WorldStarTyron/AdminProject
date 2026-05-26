<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rollen Toewijzen</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css'])
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen  transition-all duration-300">
        @include('layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden flex flex-col">
            @include('layouts.header')
            <main class="flex-1 p-8">

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Rollen Toewijzen</h1>
                        <p class="mt-1 text-sm text-gray-500 max-w-md">
                            Beheer welke rollen zijn toegewezen aan uw leden. Zoek een gebruiker en wijzig hun toegangsrechten direct.
                        </p>
                    </div>
                    <a href="#" onclick="openRoleModal(null)" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21V19C17 17.9391 16.5786 17.0217 15.8284 16.2716C15.0783 15.5214 14.1609 15.1 13.1 15.1H6.9C5.83913 15.1 4.92174 15.5214 4.17157 16.2716C3.42143 17.0217 3 17.9391 3 19V21M16 3.13C17.4273 3.512 18.6738 4.381 19.5401 5.599C20.4063 6.817 20.8385 8.293 20.767 9.789C20.6955 11.285 20.1245 12.703 19.1466 13.812C18.1687 14.921 16.8437 15.652 15.39 15.91M13 7C13 9.209 11.2091 11 9 11C6.79086 11 5 9.209 5 7C5 4.791 6.79086 3 9 3C11.2091 3 13 4.791 13 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Nieuwe Rol Toewijzen
                    </a>
                </div>

                {{-- Snel Rol Toewijzen --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4">Snel Rol Toewijzen</h2>
                    <form action="{{ route('rollen-beheer.assign') }}" method="POST" id="quickAssignForm">
                        @csrf
                        <div class="flex flex-wrap items-end gap-3">
                            {{-- Zoek Gebruiker met autocomplete --}}
                            <div class="flex flex-col gap-1.5 flex-1 min-w-[180px] relative">
                                <label class="text-xs text-gray-500">Zoek Gebruiker (Lid)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 21L15 15M17 11C17 14.3137 14.3137 17 11 17C7.68629 17 5 14.3137 5 11C5 7.68629 7.68629 5 11 5C14.3137 5 17 7.68629 17 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <input type="text" id="user_search" placeholder="Naam of email..." class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white text-gray-700 placeholder-gray-400" autocomplete="off">
                                    <input type="hidden" name="gebruiker_id" id="selected_user_id">

                                    <div id="autocomplete_results" class="hidden absolute bg-white border border-gray-200 max-h-[200px] overflow-y-auto z-[1000] w-full rounded-lg shadow-md top-full left-0"></div>
                                </div>
                            </div>

                            {{-- Selecteer Rol --}}
                            <div class="flex flex-col gap-1.5 flex-1 min-w-[160px]">
                                <label class="text-xs text-gray-500">Selecteer Rol</label>
                                <select name="rol_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white text-gray-700">
                                    <option value="">Kies een rol...</option>
                                    @foreach($alleRollen as $rol)
                                        <option value="{{ $rol->rol_id }}">{{ $rol->naam }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tijdelijk Wachtwoord --}}
                            <div class="flex flex-col gap-1.5 flex-1 min-w-[160px]">
                                <label class="text-xs text-gray-500">Tijdelijk Wachtwoord (optioneel)</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="tijdelijk_wachtwoord" placeholder="Wachtwoord..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white text-gray-700 placeholder-gray-400">
                                </div>
                            </div>

                            <button type="submit" class="bg-gray-900 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                                Toewijzing Opslaan
                            </button>
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <input type="checkbox" name="verplicht_wijzigen" id="verplicht_wachtwoord" value="1" class="w-3.5 h-3.5 rounded border-gray-300 text-gray-900 focus:ring-gray-400 cursor-pointer">
                            <label for="verplicht_wachtwoord" class="text-xs text-gray-500 cursor-pointer">Verplicht wachtwoord wijzigen bij eerste inlog</label>
                        </div>
                    </form>
                </div>

                {{-- Bottom section: table + tips --}}
                <div class="flex gap-5 items-start">
                    {{-- Gebruikers & Rollen tabel --}}
                    <div class="flex-1 bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <h2 class="text-sm font-semibold text-gray-700">Gebruikers & Rollen</h2>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span>Filter:</span>
                                <form method="GET" action="{{ route('rollen-beheer') }}" class="flex items-center gap-2">
                                    <select name="rol_id" onchange="this.form.submit()" class="text-xs border-gray-200 rounded-lg py-1 px-2">
                                        <option value="">Alle Rollen</option>
                                        @foreach($alleRollen as $rol)
                                            <option value="{{ $rol->rol_id }}" {{ request('rol_id') == $rol->rol_id ? 'selected' : '' }}>{{ $rol->naam }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="search" placeholder="Zoek gebruiker..." value="{{ request('search') }}" class="text-xs border border-gray-200 rounded-lg px-2 py-1 w-40">
                                    <button type="submit" class="text-xs bg-gray-100 px-2 py-1 rounded">Filter</button>
                                    @if(request('search') || request('rol_id'))
                                        <a href="{{ route('rollen-beheer') }}" class="text-xs text-red-500">wis</a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Gebruiker</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Huidige Rol(len)</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Laatst Gewijzigd</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Acties</th>
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
                                                <p class="text-xs text-gray-400">{{ $gebruiker->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($gebruiker->rollen as $rol)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium 
                                                    {{ $rol->naam == 'Applicatie Beheerder' ? 'bg-gray-800 text-white' : 
                                                       ($rol->naam == 'Voorzitter' ? 'bg-gray-100 text-gray-700 border border-gray-200' : 
                                                       ($rol->naam == 'Administratie Medewerker' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 
                                                       'bg-gray-100 text-gray-700 border border-gray-200')) }}">
                                                    {{ $rol->naam }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400 text-xs">Geen rol</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-gray-500">
                                        {{ $gebruiker->bijgewerkt_op ? $gebruiker->bijgewerkt_op->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <button type="button" onclick="openRoleModal({{ $gebruiker->gebruiker_id }})" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition-colors">
                                            Bewerk Rol
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-400">Geen gebruikers gevonden.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
                            <span class="text-xs text-gray-400">
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
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3 class="text-xs font-semibold text-gray-700">Beveiligingstips</h3>
                        </div>
                        <ul class="flex flex-col gap-3">
                            <li class="flex items-start gap-2">
                                <svg class="shrink-0 mt-0.5" width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18457 2.99721 7.13633 4.39828 5.49707C5.79935 3.85782 7.69279 2.71538 9.79619 2.24015C11.8996 1.76491 14.1003 1.98234 16.07 2.86M22 4L12 14.01L9 11.01" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-xs text-gray-500 leading-relaxed">Gebruik het principe van 'least privilege': geef gebruikers alleen de machtigingen die ze echt nodig hebben.</p>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="shrink-0 mt-0.5" width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18457 2.99721 7.13633 4.39828 5.49707C5.79935 3.85782 7.69279 2.71538 9.79619 2.24015C11.8996 1.76491 14.1003 1.98234 16.07 2.86M22 4L12 14.01L9 11.01" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-xs text-gray-500 leading-relaxed">Review maandelijks de lijst met actieve beheerders.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </main>

            <footer class="border-t border-gray-200 px-8 py-4 flex items-center justify-between text-xs text-gray-400">
                <span>© 2024 MemberAdmin. Alle rechten voorbehouden.</span>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-gray-600 transition-colors">Privacybeleid</a>
                    <a href="#" class="hover:text-gray-600 transition-colors">Gebruiksvoorwaarden</a>
                    <a href="#" class="hover:text-gray-600 transition-colors">Support</a>
                </div>
            </footer>
        </div>
    </div>

    {{-- Modal for editing user roles --}}
    <div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold mb-4">Rollen bewerken</h3>
            <form id="roleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rollen</label>
                    <div id="roleCheckboxes" class="space-y-2">
                        {{-- Dynamic checkboxes will be injected here --}}
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2 border rounded-lg">Annuleren</button>
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg">Opslaan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.alleRollenData = @json($alleRollen);
        window.updateRolUrl   = "{{ url('/rollen-beheer/gebruiker') }}";
        window.searchUsersUrl = "{{ route('rollen-beheer.search') }}";
        window.csrfToken      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>

    @vite(['resources/js/app.js', 'resources/js/Rolbeheer.js'])
</body>
</html>