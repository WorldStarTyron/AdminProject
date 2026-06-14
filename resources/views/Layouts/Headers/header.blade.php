
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

           
             
            

            {{-- Avatar --}}
            <div class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center">
                <i class="fa-regular fa-circle-user text-white text-lg"></i>
            </div>

        </div>
    </div>
</header>