 
                <!-- Table Section -->
                <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 mb-2">Leden Table</h3>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </div>
                                <input type="text" placeholder="Search" class="pl-10 pr-4 py-1.5 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 focus:border-transparent w-64 text-slate-600 bg-transparent">
                            </div>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600 bg-slate-50 p-1.5 rounded-md self-start mt-[-8px]">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs font-semibold text-slate-800 border-b border-slate-100">
                                    <th class="py-4 px-2 w-12">ID</th>
                                    <th class="py-4 px-2">Name</th>
                                    <th class="py-4 px-2">Telefoon</th>
                                    <th class="py-4 px-2">Adress</th>
                                    <th class="py-4 px-2">Betaaling Status</th>
                                    <th class="py-4 px-2 w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2 text-slate-500 font-medium">6</td>
                                    <td class="py-4 px-2 text-slate-800 font-semibold">Jerry Mattedi</td>
                                    <td class="py-4 px-2 text-slate-600">+597 710 4823</td>
                                    <td class="py-4 px-2 text-slate-600">Commewijne,<br><span class="text-xs text-slate-400">Pronk weg</span></td>
                                    <td class="py-4 px-2 text-slate-600 font-medium">Betaald</td>
                                    <td class="py-4 px-2 text-center">
                                        <button class="text-slate-400 hover:text-slate-700">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2 text-slate-500 font-medium">7</td>
                                    <td class="py-4 px-2 text-slate-800 font-semibold">Elianora Vasilov</td>
                                    <td class="py-4 px-2 text-slate-600">+597 742 9156</td>
                                    <td class="py-4 px-2 text-slate-600">Paramaribo,<br><span class="text-xs text-slate-400">Kawikiweg</span></td>
                                    <td class="py-4 px-2 text-slate-600 font-medium">Niet Betaald</td>
                                    <td class="py-4 px-2 text-center">
                                        <button class="text-slate-400 hover:text-slate-700">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2 text-slate-500 font-medium">8</td>
                                    <td class="py-4 px-2 text-slate-800 font-semibold">Alvis Daan</td>
                                    <td class="py-4 px-2 text-slate-600">+597 873 9482</td>
                                    <td class="py-4 px-2 text-slate-600">Paramaribo,<br><span class="text-xs text-slate-400">Herck Arronstraat</span></td>
                                    <td class="py-4 px-2 text-slate-600 font-medium">Niet Betaald</td>
                                    <td class="py-4 px-2 text-center">
                                        <button class="text-slate-400 hover:text-slate-700">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2 text-slate-500 font-medium">9</td>
                                    <td class="py-4 px-2 text-slate-800 font-semibold">Lissa Shipway</td>
                                    <td class="py-4 px-2 text-slate-600">+597 812 5674</td>
                                    <td class="py-4 px-2 text-slate-600">Commewijne,<br><span class="text-xs text-slate-400">Balibaliweg</span></td>
                                    <td class="py-4 px-2 text-slate-600 font-medium">Betaald</td>
                                    <td class="py-4 px-2 text-center">
                                        <button class="text-slate-400 hover:text-slate-700">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center mt-6">
                        <div class="flex space-x-1 items-center">
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors bg-slate-50">1</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-white bg-slate-800 shadow-sm">2</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">3</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">4</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">5</button>
                            <span class="w-8 h-8 flex items-center justify-center text-sm text-slate-400">...</span>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">20</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>