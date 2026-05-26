/**
 * Rolbeheer.js — Role management page logic
 * Uses global vars set in the Blade template:
 *   window.alleRollenData  — array of all roles
 *   window.searchUsersUrl  — URL for user autocomplete search
 *   window.updateRolUrl    — base URL for updating user roles
 *   window.csrfToken       — CSRF token
 */

document.addEventListener('DOMContentLoaded', () => {
    initAutocomplete();
});

// ─── Autocomplete for quick role assignment ──────────────────────────

function initAutocomplete() {
    const searchInput = document.getElementById('user_search');
    const resultsDiv = document.getElementById('autocomplete_results');
    const userIdField = document.getElementById('selected_user_id');

    if (!searchInput || !resultsDiv || !userIdField) return;

    let debounceTimer;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`${window.searchUsersUrl}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(users => {
                    if (!users.length) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }

                    resultsDiv.innerHTML = users.map(u => `
                        <div class="autocomplete-suggestion"
                             data-id="${u.gebruiker_id}"
                             data-name="${u.naam}">
                            <strong>${u.naam}</strong><br>
                            <small>${u.email}</small>
                        </div>
                    `).join('');
                    resultsDiv.classList.remove('hidden');
                })
                .catch(() => resultsDiv.classList.add('hidden'));
        }, 300);
    });

    // Select a suggestion
    resultsDiv.addEventListener('click', (e) => {
        const item = e.target.closest('.autocomplete-suggestion');
        if (item) {
            searchInput.value = item.dataset.name;
            userIdField.value = item.dataset.id;
            resultsDiv.classList.add('hidden');
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
    });
}

// ─── Role edit modal ─────────────────────────────────────────────────

function openRoleModal(userId) {
    if (!userId) {
        alert('Nieuw gebruikers aanmaken kan via het registratieformulier.');
        return;
    }

    // Fetch current roles for this user
    fetch(`${window.updateRolUrl}/${userId}/roles`, {
        headers: { 'Accept': 'application/json' }
    })
        .then(res => res.json())
        .then(data => {
            const roleIds = data.role_ids || [];
            const container = document.getElementById('roleCheckboxes');

            container.innerHTML = window.alleRollenData.map(role => `
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="rollen[]" value="${role.rol_id}"
                           ${roleIds.includes(role.rol_id) ? 'checked' : ''}
                           class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <span class="text-sm text-gray-700">${role.naam}</span>
                </label>
            `).join('');

            const form = document.getElementById('roleForm');
            form.action = `${window.updateRolUrl}/${userId}/roles`;

            const modal = document.getElementById('roleModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        })
        .catch(() => alert('Kan gebruikersrollen niet ophalen.'));
}

function closeRoleModal() {
    const modal = document.getElementById('roleModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Expose to global scope for onclick handlers in Blade
window.openRoleModal = openRoleModal;
window.closeRoleModal = closeRoleModal;