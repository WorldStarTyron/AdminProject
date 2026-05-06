{{-- Payments Table Section --}}
        <main class="px-8 pb-8 flex-1">
            <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 p-7 animate-[fadeSlideUp_0.5s_ease-out]">

                {{-- Table Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-7 gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Betaling per lid</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="openBetalingModal" class="inline-flex items-center gap-2 bg-gradient-to-br from-[#1e3a8a] to-[#2563eb] text-white font-['Inter',sans-serif] text-[0.8125rem] font-semibold px-5 py-2.5 rounded-[10px] shadow-[0_2px_8px_rgba(30,58,138,0.2)] hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(30,58,138,0.3)] active:translate-y-0 transition-all duration-200 cursor-pointer border-none whitespace-nowrap">
                            Voeg Betaling
                        </button>
                        <button class="bg-slate-100 border-[1.5px] border-slate-200 rounded-[10px] w-[38px] h-[38px] flex items-center justify-center cursor-pointer text-slate-500 hover:bg-slate-200 hover:border-slate-300 hover:text-[#1e3a8a] transition-all duration-200">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H21M6 12H18M10 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto rounded-[10px] border border-slate-100">
                    <table class="w-full border-collapse text-left" id="betalingTable">
                        <thead>
                            <tr>
                                <th class="px-4 py-3.5 text-[0.6875rem] font-bold text-slate-500 bg-slate-50 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Naam</th>
                                <th class="px-4 py-3.5 text-[0.6875rem] font-bold text-slate-500 bg-slate-50 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Datum</th>
                                <th class="px-4 py-3.5 text-[0.6875rem] font-bold text-slate-500 bg-slate-50 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Bedrag</th>
                                <th class="px-4 py-3.5 text-[0.6875rem] font-bold text-slate-500 bg-slate-50 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Status</th>
                                <th class="px-4 py-3.5 text-[0.6875rem] font-bold text-slate-500 bg-slate-50 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Betaling Method</th>
                                <th class="px-4 py-3.5 text-[0.6875rem] font-bold text-slate-500 bg-slate-50 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Bonnummer</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Demo rows --}}
                            <tr class="transition-colors duration-150 hover:bg-slate-50">
                                <td class="px-4 py-3.5 text-[0.8125rem] text-blue-600 font-medium border-b border-slate-100">Jerry Mattedi</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">3 April 2026</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">Srd 321</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] border-b border-slate-100">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700">Betaald</span>
                                </td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">Cash</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-500 border-b border-slate-100">
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[0.6875rem] font-mono font-semibold">BON KA-2026-0001</span>
                                </td>
                            </tr>
                            <tr class="transition-colors duration-150 hover:bg-slate-50">
                                <td class="px-4 py-3.5 text-[0.8125rem] text-blue-600 font-medium border-b border-slate-100">Elianora Vasilov</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">9 April 2026</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">Srd 313</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] border-b border-slate-100">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700">Betaald</span>
                                </td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">Cash</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-500 border-b border-slate-100"></td>
                            </tr>
                            <tr class="transition-colors duration-150 hover:bg-slate-50">
                                <td class="px-4 py-3.5 text-[0.8125rem] text-blue-600 font-medium border-b border-slate-100">Alvis Daen</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">2 April 2026</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">Srd 421</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] border-b border-slate-100">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700">Betaald</span>
                                </td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">overmaking</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-500 border-b border-slate-100"></td>
                            </tr>
                            <tr class="transition-colors duration-150 hover:bg-slate-50">
                                <td class="px-4 py-3.5 text-[0.8125rem] text-blue-600 font-medium border-b border-slate-100">Lissa Shipsey</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">10 April 2026</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">Srd 536</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] border-b border-slate-100">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700">Betaald</span>
                                </td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-600 border-b border-slate-100">overmaking</td>
                                <td class="px-4 py-3.5 text-[0.8125rem] text-slate-500 border-b border-slate-100"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-[0.8125rem] text-slate-400 font-medium">Toont 1–4 van 80 betalingen</p>
                    <div class="flex items-center gap-1">
                        <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">1</span>
                        <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-white bg-gradient-to-br from-[#1e3a8a] to-[#2563eb] shadow-[0_2px_6px_rgba(30,58,138,0.2)] cursor-default">2</span>
                        <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">3</span>
                        <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">4</span>
                        <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">5</span>
                        <span class="text-slate-300 font-semibold text-xs tracking-widest px-1">•••</span>
                        <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">20</span>
                        <a href="#" class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </main>