<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<header class="bg-white px-6 py-3">
    <div class="flex justify-between items-center">

        <!-- Left: Title -->
        <div class="font-bold text-black text-lg">
            <h1 class="text-base font-bold m-0 tracking-tight">Gebruiker Management</h1>
        </div>

        <!-- Right: Bell icon + Button -->
        <div class="flex items-center gap-4">

            <!-- Notificaties -->
            @if(Auth::user()->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder']))
                @include('Layouts.Shared.Notificatie')
            @endif

            @can('leden-beheren')
            <!-- Add Button -->
            <button class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2 transition-all shadow-sm hover:shadow-md"  
            id="openAddGebruikerModal" type="button">
                <i class="fa-solid fa-plus text-xs"></i>
                Gebruiker Toevoegen
            </button>
            @endcan

        </div>
    </div>
</header>
 @include('Layouts.AddModals.AddGebruikerModal')