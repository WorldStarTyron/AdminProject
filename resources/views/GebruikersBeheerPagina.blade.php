<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GebruikerBeheerPagina</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white">
    
    @include('layouts.Sidebars.sidebar')
    
    <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
        @include('layouts.header')
        
        <div class="w-full h-[calc(100vh-76px)]">
            <div class="w-full h-full p-1">
                <div class="ml-5">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2 mt-5 mb-1">
                        <i class="fas fa-user-cog mr-1"></i>
                        Gebruikerbeheer
                    </h2>
                    <p class="text-sm ml-12 text-gray-600">Hier kunt u alle geregistreerde applicatiegebruikers overzichtelijk bekijken en beheren.</p>
                </div>

                <!-- Stat cards -->
                <div class="flex flex-row  justify-center mt-5 gap-4 px-5">

                    <!-- Totaal Gebruikers -->
                    <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
                        <div class="w-11 h-11 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 shrink-0">
                            <i class="fas fa-users text-lg"></i>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-gray-500 font-medium">Totaal Gebruikers</span>
                            <strong class="text-3xl font-bold text-gray-900">123</strong>
                        </div>
                    </div>

                    <!-- Admins -->
                    <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
                        <div class="w-11 h-11 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 shrink-0">
                            <i class="fas fa-user-shield text-lg"></i>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-gray-500 font-medium">Administratie Medewerker</span>
                            <strong class="text-3xl font-bold text-gray-900">12</strong>
                        </div>
                    </div>

                    <!-- Voorzitter -->
                    <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
                        <div class="w-11 h-11 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 shrink-0">
                            <i class="fas fa-user-tie text-lg"></i>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-gray-500 font-medium">Voorzitter</span>
                            <strong class="text-3xl font-bold text-gray-900">5</strong>
                        </div>
                    </div>
                </div>
               

          <!-- user search bar -->
          <div class="flex-row justify-center mt-5 gap-4 px-5">
                <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex flex-row items-center gap-2 w-full">
                        <div class="relative w-full">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" class="border border-gray-200 w-full rounded-xl p-2 pl-9" placeholder="Zoek op naam, email of rol...">
                        </div>
            
                <!--Filter button-->
                    <button class="px-6 py-2 rounded-full bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm transition-all duration-200 flex items-center gap-2 whitespace-nowrap" id="filterBtn" type="button">
                        <i class="fas fa-sliders-h"></i>
                        <span>Filters</span>
                    </button>
                </div>
            </div> 
          </div>

          <!-- Users Table -->
            <div class="mt-5 px-5">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Gebruiker</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Email</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Rol</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Status</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($gebruikers as $gebruiker)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">

                    <!-- Gebruiker -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                                            <i class="fas fa-user text-gray-400 text-sm"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-900">{{ $gebruiker->naam }}</span>
                                            <span class="text-xs text-gray-400">ID: #{{ $gebruiker->gebruiker_id }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4 text-gray-600">{{ $gebruiker->email }}</td>

                                <!-- Rol -->
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-medium">
                                        {{ $gebruiker->rol->naam ?? '-' }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    @if($gebruiker->status === 'Actief')
                            <span class="flex items-center gap-1.5 text-green-600 font-medium">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                Actief
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-gray-400 font-medium">
                                <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                                Inactief
                            </span>
                        @endif
                    </td>

                    <!-- Acties -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <button class="text-gray-400 hover:text-blue-500 transition-colors duration-150" title="Bewerken">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button class="text-gray-400 hover:text-red-500 transition-colors duration-150" title="Verwijderen">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
            <span class="text-sm text-gray-500">
                Toon {{ $gebruikers->firstItem() }} tot {{ $gebruikers->lastItem() }} van {{ $gebruikers->total() }} gebruikers
            </span>
            <div class="flex items-center gap-1">
                <!-- Vorige -->
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

                <!-- Volgende -->
                <a href="{{ $gebruikers->nextPageUrl() }}"
                   class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors duration-150 {{ !$gebruikers->hasMorePages() ? 'pointer-events-none opacity-40' : '' }}">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>
        </div>

    </div>
</div>



            </div>
        </div>
    </div>

</body>
</html>