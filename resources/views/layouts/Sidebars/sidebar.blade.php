<aside class="fixed top-0 left-0 flex flex-col h-screen w-64 bg-white border-r border-gray-200 transition-all duration-300 z-40" id="mainSidebar">

    {{-- Font Awesome CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Toggle Button --}}
    <div class="flex items-center px-4 py-3">
        <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors" id="sidebarToggle" title="Toggle sidebar">
            <i class="fa-solid fa-bars w-5 h-5 flex items-center justify-center text-base"></i>
        </button>
    </div>

    <div class="border-t border-gray-200 mx-3"></div>

    {{-- Navigation --}}
    <p class="px-4 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Algemeen</p>
    <nav class="flex-1 overflow-y-auto px-3 pb-2">
        <ul class="space-y-1">

            <!-- Dashboard -->
            <li class="rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Dashboard">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-house text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Menu -->
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu</p>

            <!-- Leden -->
            <li class="rounded-lg {{ request()->routeIs('ledenpagina') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('ledenpagina') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Leden">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-users text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Leden</span>
                </a>
            </li>

            <!-- Betaling -->
            <li class="rounded-lg {{ request()->routeIs('betalingPagina') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('betalingPagina') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Betaling">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-credit-card text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Betaling</span>
                </a>
            </li>

            <!-- Rollen beheren -->
            <li class="rounded-lg {{ request()->routeIs('rollen-beheer') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('rollen-beheer') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Rollen">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-user-shield text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Rollen</span>
                </a>
            </li>

            <!-- Gebruikers beheren -->
            <li class="rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                <a href="{{ route('GebruikersBeheer') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Gebruikers">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-user-gear text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Gebruikers</span>
                </a>
            </li>

            <!-- Rapport -->
            <li class="rounded-lg {{ request()->routeIs('rapport') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="{{ route('Rapport') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Rapport">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-chart-bar text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Rapport</span>
                </a>
            </li>

            <!-- Log Activiteit -->
            <li class="rounded-lg {{ request()->routeIs('log-activiteit') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors" data-tooltip="Log Activiteit">
                    <span class="shrink-0 w-5 text-center">
                        <i class="fa-solid fa-clipboard-list text-base"></i>
                    </span>
                    <span class="nav-text text-sm font-medium">Log Activiteit</span>
                </a>
            </li>

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