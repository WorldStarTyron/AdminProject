{{-- Zorg dat Alpine.js geladen is, ook voor rollen zonder notificatieblok (bijv. Voorzitter) --}}
<script>
    if (typeof window.Alpine === 'undefined' && !document.getElementById('alpine-cdn')) {
        var alpineScript = document.createElement('script');
        alpineScript.id = 'alpine-cdn';
        alpineScript.defer = true;
        alpineScript.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
        document.head.appendChild(alpineScript);
    }
</script>

<header class="main-header">
    <div class="header-container">

        <div class="header-left">
            <h1 class="header-title">Administratie Panel</h1>
        </div>

        <div class="header-right flex items-center gap-4">

            <!-- Notificaties -->
            @if(Auth::user()->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder']))
                @include('Layouts.Shared.Notificatie')
            @endif

       <div class="flex items-center gap-4" x-data="{ open: false }">

    <!-- Avatar + Naam + Rollen -->
    <div class="relative">
        <button @click="open = !open" @click.outside="open = false"
                class="flex items-center gap-2 cursor-pointer focus:outline-none">

            <!-- Naam + Rollen -->
            <div class="flex flex-col text-right">
                <span class="text-sm font-semibold text-gray-900">{{ Auth::user()->naam }}</span>
                <div class="flex items-center justify-end gap-1 flex-wrap mt-0.5">
                    @forelse(Auth::user()->rollen as $rol)
                        <span class="text-xs text-gray-500">
                            @if($rol->naam === 'Admin')
                                <i class="fa-solid fa-shield-halved"></i>
                            @elseif($rol->naam === 'Voorzitter')
                                <i class="fa-solid fa-star"></i>
                            @elseif($rol->naam === 'Lid')
                                <i class="fa-solid fa-user"></i>
                            @else
                                <i class="fa-solid fa-circle-user"></i>
                            @endif
                            {{ $rol->naam }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400">Geen rol</span>
                    @endforelse
                </div>
            </div>

            <!-- Avatar -->
            <div class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center">
                <i class="fa-regular fa-circle-user text-white text-lg"></i>
            </div>

        </button>

        <!-- Dropdown -->
        <div x-show="open" x-transition
             class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
            <ul class="py-1">
               
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket"></i> Uitloggen
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

</div>

        </div>
    </div>
</header>