// Dit bestand controleert de invoer bij het toevoegen van een lid
// We laten meteen zien of iets goed of fout is

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('addLidForm');
    if (!form) return;

    // === Stap 1: Alle velden ophalen ===
    const fields = {
        name:           document.getElementById('name'),
        email:          document.getElementById('email'),
        telefoonnummer: document.getElementById('telefoonnummer'),
        woonplaats:     document.getElementById('woonplaats'),
        adres:          document.getElementById('adres'),
        geboortedatum:  document.getElementById('geboortedatum'),
    };

    const submitBtn = document.getElementById('submitBtn');


    // === Stap 2: Controles ===

    function validateName(value) {
        value = value.trim();
        if (value.length === 0)  return 'Naam is verplicht.';
        if (value.length < 2)    return 'Naam moet minstens 2 tekens zijn.';
        if (value.length > 255)  return 'Naam mag maximaal 255 tekens zijn.';

        var pattern = /^[a-zA-ZÀ-ÿ\s\-\.]+$/;
        if (!pattern.test(value)) return 'Naam mag alleen letters en spaties hebben.';

        return null;
    }

    function validateEmail(value) {
        value = value.trim();
        if (value.length === 0) return 'Email is verplicht.';
        if (value.length > 255) return 'Email mag maximaal 255 tekens zijn.';

        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!pattern.test(value)) return 'Voer een echte email in.';

        return null;
    }

    function validateTelefoon(value) {
        value = value.trim();
        if (value.length === 0) return 'Telefoonnummer is verplicht.';

        var pattern = /^[\+]?[0-9\s\-]+$/;
        if (!pattern.test(value)) return 'Alleen cijfers en + toegestaan.';

        var digitsOnly = value.replace(/[^0-9]/g, '');
        if (digitsOnly.length < 7)  return 'Telefoonnummer moet minstens 7 cijfers hebben.';
        if (digitsOnly.length > 15) return 'Telefoonnummer mag maximaal 15 cijfers hebben.';

        return null;
    }

    function validateWoonplaats(value) {
        if (!value || value.length === 0) return 'Kies een woonplaats.';

        var allowed = [
            'Latour', 'Paramaribo', 'Wanica', 'Nickerie', 'Commewijne',
            'Saramacca', 'Para', 'Coronie', 'Marowijne', 'Brokopondo'
        ];
        if (allowed.indexOf(value) === -1) return 'Kies een geldige woonplaats.';

        return null;
    }

    function validateAdres(value) {
        value = value.trim();
        if (value.length === 0) return 'Adres is verplicht.';
        if (value.length < 5)   return 'Adres moet minstens 5 tekens zijn.';
        if (value.length > 255) return 'Adres mag maximaal 255 tekens zijn.';

        return null;
    }

    function validateGeboortedatum(value) {
        if (!value) return 'Geboortedatum is verplicht.';

        var date = new Date(value);
        var today = new Date();
        today.setHours(0, 0, 0, 0);

        if (isNaN(date.getTime())) return 'Voer een geldige datum in.';
        if (date >= today) return 'Geboortedatum moet in het verleden liggen.';

        var minDate = new Date('1920-01-01');
        if (date < minDate) return 'Geboortedatum is ongeldig.';

        var age = today.getFullYear() - date.getFullYear();
        var monthDiff = today.getMonth() - date.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < date.getDate())) {
            age--;
        }
        if (age < 10) return 'Lid moet minstens 10 jaar oud zijn.';

        return null;
    }


    // === Stap 3: Foutmeldingen tonen ===

    function showFieldError(field, message) {
        removeFieldError(field);
        field.classList.add('field-error');
        field.classList.remove('field-valid');

        var errorSpan = document.createElement('span');
        errorSpan.className = 'field-error-msg';
        errorSpan.textContent = message;
        field.parentNode.appendChild(errorSpan);
    }

    function removeFieldError(field) {
        field.classList.remove('field-error');

        var existing = field.parentNode.querySelector('.field-error-msg');
        if (existing) existing.remove();
    }

    function showFieldValid(field) {
        removeFieldError(field);
        field.classList.add('field-valid');
    }


    // === Stap 4: Validatoren koppelen ===

    var validators = {
        name:           validateName,
        email:          validateEmail,
        telefoonnummer: validateTelefoon,
        woonplaats:     validateWoonplaats,
        adres:          validateAdres,
        geboortedatum:  validateGeboortedatum,
    };


    // === Stap 5: Eén veld controleren ===

    function checkField(fieldName) {
        var field = fields[fieldName];
        if (!field) return false;

        var value = field.value;
        var error = validators[fieldName](value);

        if (error) {
            showFieldError(field, error);
            return false;
        } else {
            showFieldValid(field);
            return true;
        }
    }


    // === Stap 6: Alle velden controleren ===

    function checkAllFields() {
        var allValid = true;

        var fieldNames = Object.keys(fields);
        for (var i = 0; i < fieldNames.length; i++) {
            var isValid = checkField(fieldNames[i]);
            if (!isValid) allValid = false;
        }

        return allValid;
    }

    window.checkAllFields = checkAllFields;


    // === Stap 7: Controleren terwijl je typt ===

    var textFields = ['name', 'email', 'telefoonnummer', 'adres'];
    textFields.forEach(function (fieldName) {
        var field = fields[fieldName];
        if (!field) return;

        var timer = null;

        field.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                if (field.value.length > 0) {
                    checkField(fieldName);
                }
            }, 400);
        });

        field.addEventListener('blur', function () {
            checkField(fieldName);
        });
    });

    var selectFields = ['woonplaats'];
    selectFields.forEach(function (fieldName) {
        var field = fields[fieldName];
        if (!field) return;

        field.addEventListener('change', function () {
            checkField(fieldName);
        });
    });

    if (fields.geboortedatum) {
        fields.geboortedatum.addEventListener('change', function () {
            checkField('geboortedatum');
        });
    }


    // === Stap 8: Wissen bij sluiten ===

    window.resetFormValidation = function () {
        var fieldNames = Object.keys(fields);
        for (var i = 0; i < fieldNames.length; i++) {
            var field = fields[fieldNames[i]];
            if (field) {
                removeFieldError(field);
                field.classList.remove('field-valid');
            }
        }
    };

});
