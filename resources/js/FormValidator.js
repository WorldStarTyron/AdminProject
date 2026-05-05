// FormValidator.js
// =================
// This file checks each form field IN REAL-TIME as the admin types.
// It shows red error messages under each field so the admin can fix mistakes BEFORE submitting.
//
// HOW IT WORKS:
// 1. When the admin types in a field, this code checks if the value is correct.
// 2. If the value is wrong, a red message appears under the field.
// 3. If the value is correct, a green checkmark appears.
// 4. The submit button is disabled until ALL fields are valid.

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('addLidForm');
    if (!form) return; // stop if the form is not on this page

    // ==============================
    // STEP 1: Get all form fields
    // ==============================
    const fields = {
        name:           document.getElementById('name'),
        email:          document.getElementById('email'),
        telefoonnummer: document.getElementById('telefoonnummer'),
        woonplaats:     document.getElementById('woonplaats'),
        adres:          document.getElementById('adres'),
        geboortedatum:  document.getElementById('geboortedatum'),
    };

    const submitBtn = document.getElementById('submitBtn');


    // ==============================
    // STEP 2: Validation rules
    // Each function returns an error message (string) or null (no error).
    // ==============================

    function validateName(value) {
        value = value.trim();
        if (value.length === 0)  return 'Naam is verplicht.';
        if (value.length < 2)    return 'Naam moet minstens 2 tekens bevatten.';
        if (value.length > 255)  return 'Naam mag maximaal 255 tekens bevatten.';

        // Only letters, spaces, hyphens, dots allowed
        var pattern = /^[a-zA-ZÀ-ÿ\s\-\.]+$/;
        if (!pattern.test(value)) return 'Naam mag alleen letters, spaties en streepjes bevatten.';

        return null; // no error
    }

    function validateEmail(value) {
        value = value.trim();
        if (value.length === 0) return 'Email is verplicht.';
        if (value.length > 255) return 'Email mag maximaal 255 tekens bevatten.';

        // Simple email pattern check
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!pattern.test(value)) return 'Voer een geldig e-mailadres in (bijv. naam@voorbeeld.com).';

        return null;
    }

    function validateTelefoon(value) {
        value = value.trim();
        if (value.length === 0) return 'Telefoonnummer is verplicht.';

        // Only digits, +, spaces, hyphens allowed
        var pattern = /^[\+]?[0-9\s\-]+$/;
        if (!pattern.test(value)) return 'Alleen cijfers, +, spaties en streepjes toegestaan.';

        // Count only the digits
        var digitsOnly = value.replace(/[^0-9]/g, '');
        if (digitsOnly.length < 7)  return 'Telefoonnummer moet minstens 7 cijfers bevatten.';
        if (digitsOnly.length > 15) return 'Telefoonnummer mag maximaal 15 cijfers bevatten.';

        return null;
    }

    function validateWoonplaats(value) {
        if (!value || value.length === 0) return 'Selecteer een woonplaats.';

        var allowed = [
            'Latour', 'Paramaribo', 'Wanica', 'Nickerie', 'Commewijne',
            'Saramacca', 'Para', 'Coronie', 'Marowijne', 'Brokopondo'
        ];
        if (allowed.indexOf(value) === -1) return 'Selecteer een geldige woonplaats.';

        return null;
    }

    function validateAdres(value) {
        value = value.trim();
        if (value.length === 0) return 'Adres is verplicht.';
        if (value.length < 5)   return 'Adres moet minstens 5 tekens bevatten.';
        if (value.length > 255) return 'Adres mag maximaal 255 tekens bevatten.';

        return null;
    }

    function validateGeboortedatum(value) {
        if (!value) return 'Geboortedatum is verplicht.';

        var date = new Date(value);
        var today = new Date();
        today.setHours(0, 0, 0, 0);

        // Check if it's a real date
        if (isNaN(date.getTime())) return 'Voer een geldige datum in.';

        // Must be in the past
        if (date >= today) return 'Geboortedatum moet in het verleden liggen.';

        // Must be after 1920
        var minDate = new Date('1920-01-01');
        if (date < minDate) return 'Geboortedatum is ongeldig.';

        // Member should be at least 10 years old
        var age = today.getFullYear() - date.getFullYear();
        var monthDiff = today.getMonth() - date.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < date.getDate())) {
            age--;
        }
        if (age < 10) return 'Lid moet minstens 10 jaar oud zijn.';

        return null;
    }


    // ==============================
    // STEP 3: Show/hide error messages
    // ==============================

    // Create an error message element under a field
    function showFieldError(field, message) {
        removeFieldError(field);
        field.classList.add('field-error');
        field.classList.remove('field-valid');

        var errorSpan = document.createElement('span');
        errorSpan.className = 'field-error-msg';
        errorSpan.textContent = message;
        field.parentNode.appendChild(errorSpan);
    }

    // Remove the error message from a field
    function removeFieldError(field) {
        field.classList.remove('field-error');

        var existing = field.parentNode.querySelector('.field-error-msg');
        if (existing) existing.remove();
    }

    // Show green checkmark when field is valid
    function showFieldValid(field) {
        removeFieldError(field);
        field.classList.add('field-valid');
    }


    // ==============================
    // STEP 4: Map each field to its validator
    // ==============================

    var validators = {
        name:           validateName,
        email:          validateEmail,
        telefoonnummer: validateTelefoon,
        woonplaats:     validateWoonplaats,
        adres:          validateAdres,
        geboortedatum:  validateGeboortedatum,
    };


    // ==============================
    // STEP 5: Check one field
    // ==============================

    function checkField(fieldName) {
        var field = fields[fieldName];
        if (!field) return false;

        var value = field.value;
        var error = validators[fieldName](value);

        if (error) {
            showFieldError(field, error);
            return false; // field is invalid
        } else {
            showFieldValid(field);
            return true; // field is valid
        }
    }


    // ==============================
    // STEP 6: Check ALL fields (used before submitting)
    // ==============================

    function checkAllFields() {
        var allValid = true;

        var fieldNames = Object.keys(fields);
        for (var i = 0; i < fieldNames.length; i++) {
            var isValid = checkField(fieldNames[i]);
            if (!isValid) allValid = false;
        }

        return allValid;
    }

    // Make this function available to AddLidModal.js
    window.checkAllFields = checkAllFields;


    // ==============================
    // STEP 7: Listen for typing and changes
    // When the admin types, check that field immediately.
    // ==============================

    // Text fields: check when typing stops (after a short delay)
    var textFields = ['name', 'email', 'telefoonnummer', 'adres'];
    textFields.forEach(function (fieldName) {
        var field = fields[fieldName];
        if (!field) return;

        var timer = null;

        // Check after admin stops typing for 400ms
        field.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                // Only validate if the field has been touched (has some content)
                if (field.value.length > 0) {
                    checkField(fieldName);
                }
            }, 400);
        });

        // Also check when the admin clicks away from the field
        field.addEventListener('blur', function () {
            checkField(fieldName);
        });
    });

    // Dropdown fields: check immediately when changed
    var selectFields = ['woonplaats'];
    selectFields.forEach(function (fieldName) {
        var field = fields[fieldName];
        if (!field) return;

        field.addEventListener('change', function () {
            checkField(fieldName);
        });
    });

    // Date field: check when changed
    if (fields.geboortedatum) {
        fields.geboortedatum.addEventListener('change', function () {
            checkField('geboortedatum');
        });
    }


    // ==============================
    // STEP 8: Reset validation when modal closes
    // ==============================

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
