// Dit bestand zorgt voor het herstellen van een betaling
// We vragen of je het zeker bent en sturen dan de gegevens naar de server

// Waarschuwing scherm maken en in de pagina zetten
var waarschuwingHTML = `
<style>
    @keyframes waarschuwingPop {
        0% {
            opacity: 0;
            transform: scale(0.95) translateY(-8px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    #herstel-waarschuwing .modal-content {
        animation: waarschuwingPop 0.2s ease-out forwards;
    }
</style>

<div id="herstel-waarschuwing" style="
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.45);
    z-index: 9999;
    align-items: center;
    justify-content: center;
">
    <div class="modal-content" style="
        background: #fff;
        border-radius: 16px;
        padding: 28px 28px 24px;
        max-width: 380px;
        width: calc(100% - 48px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        font-family: 'Inter', sans-serif;
    ">

        <!-- Icoon -->
        <div style="
            width: 48px; height: 48px;
            background: #fef9c3;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px auto;
        ">
            <i class="fa-solid fa-triangle-exclamation" style="color: #ca8a04; font-size: 20px;"></i>
        </div>

        <!-- Tekst -->
        <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 6px;">Betaling herstellen</p>
        <p style="font-size: 13px; color: #64748b; margin: 0 0 24px;">Weet je zeker dat je deze betaling wilt herstellen? De betaling komt terug in het overzicht.</p>

        <!-- Knoppen -->
        <div style="display: flex; gap: 10px;">
            <button id="herstel-annuleer" style="
                flex: 1;
                padding: 10px;
                border: none;
                border-radius: 10px;
                background: #f1f5f9;
                color: #475569;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.15s;
            ">Annuleren</button>
            <button id="herstel-bevestig" style="
                flex: 1;
                padding: 10px;
                border: none;
                border-radius: 10px;
                background: #16a34a;
                color: #fff;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.15s;
            ">Ja, herstellen</button>
        </div>
    </div>
</div>
`;
document.body.insertAdjacentHTML('beforeend', waarschuwingHTML);

// Elementen ophalen
var overlay     = document.getElementById('herstel-waarschuwing');
var annuleerBtn = document.getElementById('herstel-annuleer');
var bevestigBtn = document.getElementById('herstel-bevestig');
var actieveKnop = null;

// Waarschuwing sluiten
function sluitWaarschuwing() {
    overlay.style.display = 'none';
    actieveKnop = null;
}

// Klik op herstel knop → toon waarschuwing
document.querySelectorAll('.restore-betaling-btn').forEach(function(knop) {
    knop.addEventListener('click', function() {
        actieveKnop = this;
        overlay.style.display = 'flex';
    });
});

// Annuleer → sluit
annuleerBtn.addEventListener('click', sluitWaarschuwing);

// Klik buiten → sluit ook
overlay.addEventListener('click', function(e) {
    if (e.target === overlay) sluitWaarschuwing();
});

// Bevestig → verstuur
bevestigBtn.addEventListener('click', function() {
    if (!actieveKnop) return;

    var id  = actieveKnop.dataset.id;
    var rij = actieveKnop.closest('tr');

    bevestigBtn.textContent = 'Bezig...';
    bevestigBtn.disabled    = true;

    fetch('/betalingen/' + id + '/restore', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            rij.remove();
            sluitWaarschuwing();
        } else {
            alert(data.message);
        }
    })
    .catch(function() {
        alert('Er is iets misgegaan. Probeer opnieuw.');
    })
    .finally(function() {
        bevestigBtn.textContent = 'Ja, herstellen';
        bevestigBtn.disabled    = false;
    });
});
