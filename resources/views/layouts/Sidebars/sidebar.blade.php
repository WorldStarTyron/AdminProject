<aside class="fixed top-0 left-0 flex flex-col h-screen w-64 bg-white border-r border-gray-200 transition-all duration-300 z-40" id="mainSidebar">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Toggle Button -->
    <div class="flex items-center px-4 py-3">
        <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors" id="sidebarToggle" title="Toggle sidebar">
            <i class="fa-solid fa-bars w-5 h-5 flex items-center justify-center text-base"></i>
        </button>
    </div>

    <div class="border-t border-gray-200 mx-3"></div>

    <!-- Navigation -->
    <p class="px-4 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Algemeen</p>
    <nav class="flex-1 overflow-y-auto px-3 pb-2">
        <ul class="space-y-1">

            <!-- Dashboard — zichtbaar voor iedereen behalve Lid -->
            @can('dashboard')
            <li class="rounded-lg {{ request()->routeIs('MainDashboardPagina') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('MainDashboardPagina') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Dashboard">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-house text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Dashboard</span>
                </a>
            </li>
            @endcan

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu</p>

            <!-- Mijn Gegevens — zichtbaar voor elke gebruiker met de Lid rol (ook als ze andere rollen hebben) -->
            @can('eigen-profiel')
            <li class="rounded-lg {{ request()->routeIs('GegevensPagina') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('GegevensPagina') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Mijn Gegevens">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-id-card text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Mijn Gegevens</span>
                </a>
            </li>
            @endcan

            <!-- Leden — alleen admin medewerker en applicatiebeheerder -->
            @can('leden-bekijken')
            <li class="rounded-lg {{ request()->routeIs('ledenpagina') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('ledenpagina') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Leden">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-users text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Leden</span>
                </a>
            </li>
            @endcan

            <!-- Betaling — alleen admin medewerker en applicatiebeheerder -->
            @can('betalingen-bekijken')
            <li class="rounded-lg" id="betalingDropdownContainer" data-active="{{ request()->routeIs('betalingPagina', 'DeletedRecords') ? 'true' : 'false' }}">

                {{-- Dropdown toggle knop --}}
                <button
                    id="betalingDropdownToggle"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg w-full transition-colors
                           {{ request()->routeIs('betalingPagina', 'DeletedRecords') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    data-tooltip="Betaling">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-credit-card text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium flex-1 text-left">Betaling</span>
                    <span class="nav-text shrink-0">
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" id="betalingDropdownChevron"></i>
                    </span>
                </button>

                {{-- Dropdown items --}}
                <ul class="mt-1 ml-5 space-y-1 border-l border-gray-200 pl-3 hidden" id="betalingDropdownMenu">

                    <li class="rounded-lg {{ request()->routeIs('betalingPagina') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <a href="{{ route('betalingPagina') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors text-sm"
                           data-tooltip="Betalingen">
                            <i class="fa-solid fa-list text-xs shrink-0"></i>
                            <span class="nav-text font-medium">Betalingen</span>
                        </a>
                    </li>

                    @can('betalingen-verwijderen')
                    <li class="rounded-lg {{ request()->routeIs('DeletedRecords') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <a href="{{ route('DeletedRecords') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors text-sm"
                           data-tooltip="Verwijderde Betalingen">
                            <i class="fa-solid fa-trash-can text-xs shrink-0"></i>
                            <span class="nav-text font-medium">Verwijderde Betalingen</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

            <!-- Rollen beheren — alleen applicatiebeheerder -->
            @can('rollenbeheer')
            <li class="rounded-lg {{ request()->routeIs('rollen-beheer') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('rollen-beheer') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Rollen">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-user-shield text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Rollen</span>
                </a>
            </li>
            @endcan

            <!-- Gebruikers beheren — alleen applicatiebeheerder -->
            @can('gebruikersbeheer')
            <li class="rounded-lg {{ request()->routeIs('GebruikersBeheer') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('GebruikersBeheer') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Gebruikers">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-user-gear text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Gebruikers</span>
                </a>
            </li>
            @endcan

            <!-- Rapport — voorzitter en applicatiebeheerder -->
            @can('rapport-bekijken')
            <li class="rounded-lg {{ request()->routeIs('Rapport') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('Rapport') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Rapport">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-chart-bar text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Rapport</span>
                </a>
            </li>
            @endcan

            <!-- Log Activiteit — alleen applicatiebeheerder -->
            @can('activiteitlog-bekijken')
            <li class="rounded-lg {{ request()->routeIs('ActiviteitLog') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('ActiviteitLog') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Log Activiteit">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-clipboard-list text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Log Activiteit</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>

    <div class="px-3 pb-4 pt-2 border-t border-gray-200 mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors w-full" data-tooltip="Uitloggen">
                <span class="shrink-0 w-5 text-center">
                    <i class="fa-solid fa-right-from-bracket text-base"></i>
                </span>
                <span class="nav-text text-sm font-medium">Uitloggen</span>
            </button>
        </form>
    </div>

</aside>