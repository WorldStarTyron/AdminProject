<header class="main-header bg-white border-b border-gray-100 py-4 px-6 shadow-sm">
    <div class="header-container flex items-center justify-between max-w-7xl mx-auto">
        
        <div class="header-left">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Leden Overzicht</h1>
        </div>
        
        <div class="header-right flex items-center gap-6">

            @if(Auth::user()->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder']))
                <div class="flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors">
                    @include('Layouts.Shared.Notificatie')
                </div>
            @endif

            <div class="flex flex-col text-right justify-center">
                <span class="text-sm font-semibold text-gray-900 leading-tight">{{ Auth::user()->naam }}</span>
                <div class="flex items-center justify-end gap-1.5 flex-wrap mt-1">
                    @forelse(Auth::user()->rollen as $rol)
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[11px] font-medium bg-gray-50 text-gray-600 border border-gray-100">
                            @if($rol->naam === 'Admin')
                                <i class="fa-solid fa-shield-halved text-indigo-500 text-[10px]"></i>
                            @elseif($rol->naam === 'Voorzitter')
                                <i class="fa-solid fa-star text-amber-500 text-[10px]"></i>
                            @elseif($rol->naam === 'Lid')
                                <i class="fa-solid fa-user text-emerald-500 text-[10px]"></i>
                            @else
                                <i class="fa-solid fa-circle-user text-gray-400 text-[10px]"></i>
                            @endif
                            {{ $rol->naam }}
                        </span>
                    @empty
                        <span class="text-xs font-normal text-gray-400 italic">Geen rol</span>
                    @endforelse
                </div>
            </div>

            <div class="w-10 h-10 rounded-full bg-gray-900 flex items-center justify-center shadow-sm hover:opacity-90 transition-opacity cursor-pointer">
                <i class="fa-regular fa-circle-user text-white text-xl"></i>
            </div>

        </div>
    </div>
</header>
