<div id="addGebruikerModal" 
     class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-[1000] opacity-0 invisible transition-all duration-300 [&.active]:opacity-100 [&.active]:visible">

    <form id="addGebruikerForm" method="POST" action="{{ route('GebruikersBeheer.store') }}"
          class="rounded-2xl bg-white shadow-[0_25px_60px_-15px_rgba(0,0,0,0.2),0_0_0_1px_rgba(226,232,240,0.5)] p-8 w-[90vw] max-w-lg flex flex-col gap-6">
        @csrf

        <div class="flex flex-row justify-between items-start border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Voeg een gebruiker toe</h2>
                <p class="text-xs text-slate-500 mt-1">Vul de gegevens in om een nieuwe gebruiker aan te maken.</p>
            </div>
            <button type="button" id="closeAddGebruikerModalBtn" aria-label="Sluiten"
                    class="text-2xl text-slate-400 bg-transparent border-none cursor-pointer w-9 h-9 flex items-center justify-center rounded-[10px] transition-all duration-200 leading-none shrink-0 hover:text-red-500 hover:bg-red-50">
                &times;
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-h-[60vh] overflow-y-auto px-1">

            @if ($errors->any())
                <div class="md:col-span-2 bg-red-50 text-red-600 text-xs rounded-lg p-3">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="md:col-span-2 border-b border-slate-100 pb-1 mt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Algemene Informatie</span>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="add_gebruiker_naam" class="text-[0.8125rem] font-semibold text-slate-700">
                    Naam <span class="text-red-500">*</span>
                </label>
                <input type="text" id="add_gebruiker_naam" name="naam" value="{{ old('naam') }}" required
                       class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="add_gebruiker_email" class="text-[0.8125rem] font-semibold text-slate-700">
                    E-mail <span class="text-red-500">*</span>
                </label>
                <input type="email" id="add_gebruiker_email" name="email" value="{{ old('email') }}" required
                       class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
            </div>

            <div class="md:col-span-2 border-b border-slate-100 pb-1 mt-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Beveiliging</span>
            </div>

    <div class="flex flex-col gap-1.5">
    <label for="add_gebruiker_wachtwoord" class="text-[0.8125rem] font-semibold text-slate-700">
        Wachtwoord <span class="text-red-500">*</span>
    </label>

    <div class="relative">
        <input type="password" id="add_gebruiker_wachtwoord" name="wachtwoord" required
               class="px-3 py-2.5 pr-10 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">

        <button type="button" id="togglePassword1" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
            <i class="fa-solid fa-eye"></i>
            <i class="fa-solid fa-eye-slash"></i>
        </button>
    </div>
</div>

          

         <div class="flex flex-col gap-1.5">
    <label for="add_gebruiker_wachtwoord_bevestiging" class="text-[0.8125rem] font-semibold text-slate-700">
        Wachtwoord bevestiging <span class="text-red-500">*</span>
    </label>

    <div class="relative">
        <input type="password" id="add_gebruiker_wachtwoord_bevestiging" name="wachtwoord_confirmation" required
               class="px-3 py-2.5 pr-10 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">

        <button type="button" id="togglePassword2" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
            <i class="fa-solid fa-eye"></i>
            <i class="fa-solid fa-eye-slash"></i>
        </button>
       </div>
            </div>

        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 border-t border-slate-100 pt-4 mt-2">
            <button type="button" id="annuleerAddGebruikerBtn" 
                    class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors duration-200 cursor-pointer text-center">
                Annuleren
            </button>

            <button type="submit" id="submitAddGebruikerBtn"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm shadow-blue-500/10 transition-colors duration-200 cursor-pointer text-center">
                Gebruiker Toevoegen
            </button>
        </div>

    </form>
</div>