<div id="editGebruikerModal"
     class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-[1000] opacity-0 invisible transition-all duration-300 [&.active]:opacity-100 [&.active]:visible">

    <form id="editGebruikerModalForm" method="POST" action=""
          class="rounded-2xl bg-white shadow-[0_25px_60px_-15px_rgba(0,0,0,0.2),0_0_0_1px_rgba(226,232,240,0.5)] p-8 w-[90vw] max-w-lg flex flex-col gap-6">
    @csrf
    @method('PUT')
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-800">Gebruiker Wijzigen</h2>
            <p class="text-xs text-slate-500 mt-1">Pas hier de gegevens van de geselecteerde gebruiker aan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-h-[60vh] overflow-y-auto px-1">

            <div class="md:col-span-2 border-b border-slate-100 pb-1 mt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Persoonlijke Informatie</span>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="edit_gebruiker_naam" class="text-[0.8125rem] font-semibold text-slate-700">
                    Naam <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit_gebruiker_naam" name="naam" readonly
                       class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="edit_gebruiker_email" class="text-[0.8125rem] font-semibold text-slate-700">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="edit_gebruiker_email" name="email" readonly
                       class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
            </div>


            <div class="md:col-span-2 border-b border-slate-100 pb-1 mt-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Accounteinstellingen</span>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="edit_gebruiker_rol" class="text-[0.8125rem] font-semibold text-slate-700">
                    Rol <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit_gebruiker_rol" name="rol" readonly
                       class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="edit_gebruiker_status" class="text-[0.8125rem] font-semibold text-slate-700">
                    Status <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit_gebruiker_status" name="status" readonly
                       class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
            </div>



        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 border-t border-slate-100 pt-4 mt-2">
            <button type="button" id="sluit_edit_modal_btn" onclick="closeModal()"
                    class=" closeEditGebruikerModalBtn px-5 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors duration-200 cursor-pointer text-center">
                Annuleren
            </button>

            <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm shadow-blue-500/10 transition-colors duration-200 cursor-pointer text-center">
                Opslaan
            </button>
        </div>

    </form>
</div>

@vite('resources/js/EditModals/EditGebruikerModal.js')
