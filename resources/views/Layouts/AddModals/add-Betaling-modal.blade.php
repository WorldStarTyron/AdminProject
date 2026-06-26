<!-- ===== Betaling toevoegen Modal ===== -->
<div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-[1000] opacity-0 invisible transition-all duration-300 [&.active]:opacity-100 [&.active]:visible" id="addBetalingModal">
    <div class="transform translate-y-5 scale-[0.97] opacity-0 transition-all duration-[350ms] [.active_&]:translate-y-0 [.active_&]:scale-100 [.active_&]:opacity-100 w-full max-w-[640px] max-h-[90vh] overflow-y-auto m-4 scrollbar-thin">
        <div class="bg-white rounded-2xl p-8 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.2),0_0_0_1px_rgba(226,232,240,0.5)]">

            <!-- Titel en sluitknop -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight m-0">Betaling toevoegen</h2>
                    <p class="text-[0.8125rem] text-slate-400 mt-1">Vul de betalingsgegevens in om een nieuwe betaling te registreren</p>
                </div>
                <!-- Sluitknop -->
                <button type="button" id="closeBetalingModalBtn" aria-label="Sluiten"
                    class="text-slate-400 bg-transparent border-none cursor-pointer w-9 h-9 flex items-center justify-center rounded-[10px] transition-all duration-200 shrink-0 hover:text-red-500 hover:bg-red-50">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Sectie label -->
            <div class="text-[0.6875rem] font-bold text-slate-400 mb-5 uppercase tracking-widest">Betaal Details</div>

            <!-- Foutmeldingen -->
            <div class="bg-red-50 text-red-700 p-3.5 rounded-[10px] mb-5 border-l-4 border-red-500 animate-[shakeError_0.4s_ease] hidden" id="betalingModalErrors">
                <ul class="m-0 pl-5 text-[0.8125rem]" id="betalingModalErrorList"></ul>
            </div>

            <form id="addBetalingForm" data-store-url="{{ route('betalingPagina.addBetaling.store') }}">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 gap-x-7 mb-6">

                    <!-- Naam veld -->
                    <div class="flex flex-col">
                        <label for="betaling_naam" class="text-[0.8125rem] font-semibold text-slate-700 mb-1.5">
                            Naam <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="betaling_naam" name="naam" placeholder="Naam" required
                            class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 font-[inherit] placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
                    </div>

                    <!-- Betalingsbewijs upload (alleen zichtbaar bij overmaking) -->
                    <div class="flex flex-col" id="betalingBewijsContainer" style="display: none;">
                        <label for="betaling_bewijs" class="text-[0.8125rem] font-semibold text-slate-700 mb-1.5">
                            Betaling bewijs <span class="text-red-500">*</span>
                        </label>
                        <!-- Upload knop, echt input zit er onzichtbaar overheen -->
                        <div class="relative">
                            <input type="file" id="betaling_bewijs" name="betaling_bewijs" accept="image/*,.pdf"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-[2]">
                            <div id="fileUploadBtn"
                                class="inline-flex items-center gap-2 px-4 py-2.5 border-[1.5px] border-dashed border-slate-300 rounded-[10px] bg-slate-50 text-slate-600 text-[0.8125rem] font-semibold cursor-pointer transition-all duration-200 font-[inherit] w-full box-border hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50">
                                <!-- Upload icoon -->
                                <i class="fa-solid fa-upload"></i>
                                <span id="fileUploadText">Bestand uploaden</span>
                            </div>
                        </div>
                    </div>

                    <!-- Datum veld -->
                    <div class="flex flex-col">
                        <label for="betaling_datum" class="text-[0.8125rem] font-semibold text-slate-700 mb-1.5">
                            Datum van betaling <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="betaling_datum" name="datum" value="{{ date('Y-m-d') }}" required
                            class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 font-[inherit] focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
                    </div>

                    <!-- Betaalmethode -->
                    <div class="flex flex-col">
                        <label for="betaling_methode" class="text-[0.8125rem] font-semibold text-slate-700 mb-1.5">
                            Methode <span class="text-red-500">*</span>
                        </label>
                        <!-- Standaard waarde: geld -->
                        <select id="betaling_methode" name="methode" required
                            class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 font-[inherit] focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
                            <option value="" disabled selected>Select</option>
                            <option value="fysiek">Geld</option>
                            <option value="overmaking">Overmaking</option>
                        </select>
                    </div>

<!-- Status van de betaling -->
                    <div class="flex flex-col">
                        <label for="betaling_status" class="text-[0.8125rem] font-semibold text-slate-700 mb-1.5">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="betaling_status" name="status" required
                            class="px-3 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[0.8125rem] text-slate-700 bg-slate-50 w-full box-border transition-all duration-200 font-[inherit] focus:outline-none focus:border-blue-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
                            <option value="" disabled selected>Select</option>
<option value="Openstaand">Openstaand</option>
                            <option value="in_afwachting">In Afwachting</option>
                            <option value="afgewezen">Afgewezen</option>
                            <option value="niet_betaald">Niet Betaald</option>
                            <option value="betaald">Betaald</option>
                        </select>
                    </div>

                    <!-- Bedrag met SRD prefix -->
                    <div class="flex flex-col">
                        <label for="betaling_bedrag" class="text-[0.8125rem] font-semibold text-slate-700 mb-1.5">
                            Bedrag <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-stretch border-[1.5px] border-slate-200 rounded-[10px] overflow-hidden bg-slate-50 transition-all duration-200 focus-within:border-blue-600 focus-within:bg-white focus-within:shadow-[0_0_0_3px_rgba(37,99,235,0.08)]">
                            <!-- Valuta label links -->
                            <span class="flex items-center px-3 bg-slate-100 text-slate-500 text-[0.8125rem] font-semibold border-r-[1.5px] border-slate-200 whitespace-nowrap select-none">SRD</span>
                            <input type="number" id="betaling_bedrag" name="bedrag" placeholder="0.00" step="0.01" min="0" required
                                class="border-none !rounded-none bg-transparent !shadow-none flex-1 min-w-0 px-3 py-2.5 text-[0.8125rem] text-slate-700 font-[inherit] focus:outline-none focus:shadow-none">
                        </div>
                    </div>
                </div>

                <!-- Knoppen onderaan -->
                <div class="flex gap-3 mt-6 pt-6 border-t border-slate-100">
                    <!-- Opslaan knop -->
                    <button type="submit" id="submitBetalingBtn"
                        class="bg-gradient-to-br from-[#1e3a8a] to-[#2563eb] text-white px-6 py-2.5 border-none rounded-[10px] font-semibold text-[0.8125rem] cursor-pointer transition-all duration-[250ms] shadow-[0_2px_8px_rgba(30,58,138,0.2)] inline-flex items-center gap-2 font-[inherit] hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(30,58,138,0.3)] disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
                        <i class="fa-solid fa-plus"></i>
                        Betaling toevoegen
                    </button>
                    <!-- Annuleren knop -->
                    <button type="button" id="cancelBetalingModalBtn"
                        class="bg-slate-100 text-slate-600 px-6 py-2.5 border-none rounded-[10px] font-semibold text-[0.8125rem] cursor-pointer transition-all duration-200 font-[inherit] hover:bg-slate-200">
                        Annuleren
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
