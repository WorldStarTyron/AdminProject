{{-- ===== Modal Overlay ===== --}}
<div class="modal-overlay" id="addLidModal">
    <div class="modal-container">
        <div class="form-container">
            <div class="form-header">
                <div>
                    <h2 class="form-title">Nieuw lid toevoegen</h2>
                    <p class="form-description">Vul de gegevens in om een nieuw lid te registreren</p>
                </div>
                <button type="button" class="close-btn" id="closeModalBtn" aria-label="Sluiten">&times;</button>
            </div>

            <div class="form-section-title">Persoonlijke gegevens</div>

            <div class="error-container" id="modalErrors" style="display: none;">
                <ul id="modalErrorList"></ul>
            </div>

            <form id="addLidForm" data-store-url="{{ route('addlid.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Naam <span class="required">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Volledige naam" required>
                    </div>

                    <div class="form-group">
                        <label for="woonplaats">Woonplaats <span class="required">*</span></label>
                        <select id="woonplaats" name="woonplaats" required>
                            <option value="" disabled selected>Selecteer woonplaats</option>
                            <option value="Latour">Latour</option>
                            <option value="Paramaribo">Paramaribo</option>
                            <option value="Wanica">Wanica</option>
                            <option value="Nickerie">Nickerie</option>
                            <option value="Commewijne">Commewijne</option>
                            <option value="Saramacca">Saramacca</option>
                            <option value="Para">Para</option>
                            <option value="Coronie">Coronie</option>
                            <option value="Marowijne">Marowijne</option>
                            <option value="Brokopondo">Brokopondo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="email@voorbeeld.com" required>
                    </div>

                    <div class="form-group">
                        <label for="adres">Adres <span class="required">*</span></label>
                        <input type="text" id="adres" name="adres" placeholder="Straatnaam en huisnummer" required>
                    </div>

                    <div class="form-group">
                        <label for="telefoonnummer">Telefoon <span class="required">*</span></label>
                        <input type="text" id="telefoonnummer" name="telefoonnummer" placeholder="+597 ..." required>
                    </div>

                    <div class="form-group">
                        <label for="geboortedatum">Geboortedatum <span class="required">*</span></label>
                        <input type="date" id="geboortedatum" name="geboortedatum" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-add-user" id="submitBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Lid toevoegen
                    </button>
                    <button type="button" class="btn-cancel" id="cancelModalBtn">Annuleren</button>
                </div>
            </form>
        </div>
    </div>
</div>
