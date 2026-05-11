<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreLidRequest
 * 
 * This file handles all the validation rules for adding a new member.
 * Each rule makes sure the admin enters the correct information.
 * 
 * HOW IT WORKS:
 * - Laravel automatically runs these rules BEFORE the controller code runs.
 * - If any rule fails, it sends back error messages (no member is created).
 * - If all rules pass, the controller code runs normally.
 */
class StoreLidRequest extends FormRequest
{
    /**
     * Allow all users to use this form.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Validation rules for each field.
     * 
     * WHAT EACH RULE MEANS:
     * - required       = field cannot be empty
     * - string         = must be text (not a number or file)
     * - max:255        = maximum 255 characters
     * - min:2          = minimum 2 characters
     * - regex          = must match a specific pattern
     * - email          = must be a valid email format
     * - unique         = no other record in the database can have this value
     * - date           = must be a valid date
     * - before:today   = date must be in the past (not today or future)
     */
    public function rules()
    {
        return [

            // --- NAME ---
            // FIX: was 'naam', maar de form stuurt name="name"
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\.]+$/',  // only letters, spaces, hyphens, dots
            ],

            // --- EMAIL ---
            // Must be a valid email, and no duplicate in the database
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:leden,email',  // check the "leden" table, "email" column
            ],

            // --- TELEFOONNUMMER (Phone) ---
            // Must be 7-15 digits, only numbers and + allowed, no duplicate
            'telefoonnummer' => [
                'required',
                'string',
                'min:7',
                'max:20',
                'regex:/^[\+]?[0-9\s\-]+$/',      // only numbers, +, spaces, hyphens
                'unique:leden,telefoonnummer',     // check the "leden" table for duplicates
            ],

            // --- WOONPLAATS (City) ---
            // Must be one of the allowed cities in Suriname
            'woonplaats' => [
                'required',
                'string',
                'in:Latour,Paramaribo,Wanica,Nickerie,Commewijne,Saramacca,Para,Coronie,Marowijne,Brokopondo',
            ],

            // --- ADRES (Address) ---
            // Must be at least 5 characters
            'adres' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],

            // --- GEBOORTEDATUM (Date of Birth) ---
            // Must be a real date, must be in the past, member must be at least 10 years old
            'geboortedatum' => [
                'required',
                'date',
                'before:today',
                'after:1920-01-01',  // nobody born before 1920
            ],
        ];
    }

    /**
     * Custom error messages in Dutch.
     * These messages are shown to the admin when a field is wrong.
     * FIX: alle 'name.*' messages kloppen nu met de rules hierboven
     */
    public function messages()
    {
        return [
            // Name
            'name.required' => 'Naam is verplicht.',
            'name.min'      => 'Naam moet minstens 2 tekens bevatten.',
            'name.max'      => 'Naam mag maximaal 255 tekens bevatten.',
            'name.regex'    => 'Naam mag alleen letters, spaties en streepjes bevatten.',

            // Email
            'email.required' => 'Email is verplicht.',
            'email.email'    => 'Voer een geldig e-mailadres in (bijv. naam@voorbeeld.com).',
            'email.unique'   => 'Dit e-mailadres is al geregistreerd. Dit lid bestaat mogelijk al.',
            'email.max'      => 'Email mag maximaal 255 tekens bevatten.',

            // Telefoon
            'telefoonnummer.required' => 'Telefoonnummer is verplicht.',
            'telefoonnummer.min'      => 'Telefoonnummer moet minstens 7 cijfers bevatten.',
            'telefoonnummer.max'      => 'Telefoonnummer mag maximaal 20 tekens bevatten.',
            'telefoonnummer.regex'    => 'Telefoonnummer mag alleen cijfers, +, spaties en streepjes bevatten.',
            'telefoonnummer.unique'   => 'Dit telefoonnummer is al geregistreerd. Dit lid bestaat mogelijk al.',

            // Woonplaats
            'woonplaats.required' => 'Woonplaats is verplicht.',
            'woonplaats.in'       => 'Selecteer een geldige woonplaats uit de lijst.',

            // Adres
            'adres.required' => 'Adres is verplicht.',
            'adres.min'      => 'Adres moet minstens 5 tekens bevatten.',
            'adres.max'      => 'Adres mag maximaal 255 tekens bevatten.',

            // Geboortedatum
            'geboortedatum.required' => 'Geboortedatum is verplicht.',
            'geboortedatum.date'     => 'Voer een geldige datum in.',
            'geboortedatum.before'   => 'Geboortedatum moet in het verleden liggen.',
            'geboortedatum.after'    => 'Geboortedatum is ongeldig.',
        ];
    }
}