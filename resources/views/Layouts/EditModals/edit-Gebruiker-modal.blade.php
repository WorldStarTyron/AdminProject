<!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl border border-gray-100 transform scale-95 transition-transform duration-300">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-user-edit text-orange-500"></i>
                    Gebruiker bewerken
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Modal Form -->
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <!-- Naam Input -->
                    <div>
                        <label for="edit_naam" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Naam</label>
                        <input type="text" name="naam" id="edit_naam" required
                            class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-400">
                    </div>
                    
                    <!-- Email Input -->
                    <div>
                        <label for="edit_email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                        <input type="email" name="email" id="edit_email" required
                            class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-400">
                    </div>
                    
                    <!-- Status Selector -->
                    <div>
                        <label for="edit_status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" id="edit_status" required
                            class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-400">
                            <option value="Actief">Actief</option>
                            <option value="Inactief">Inactief</option>
                        </select>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors text-sm font-semibold">
                        Annuleren
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors text-sm font-semibold">
                        Opslaan
                    </button>
                </div>
            </form>
        </div>
    </div>
 @vite('resources/js/EditModals/EditGebruikerModal.js')
