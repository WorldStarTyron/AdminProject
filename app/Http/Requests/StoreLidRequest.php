<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Validatie regels voor lid toevoegen
class StoreLidRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // form stuurt 'name', niet 'naam'
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\.]+$/',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:gebruikers,email',
            ],

            'telefoonnummer' => [
                'required',
                'string',
                'min:7',
                'max:20',
                'regex:/^[\+]?[0-9\s\-]+$/',
                'unique:leden,telefoonnummer',
            ],

            // Lijst districten van Suriname
            'woonplaats' => [
                'required',
                'string',
                'in:Latour,Paramaribo,Wanica,Nickerie,Commewijne,Saramacca,Para,Coronie,Marowijne,Brokopondo',
            ],

            'adres' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],

            'geboortedatum' => [
                'required',
                'date',
                'before:today',
                'after:1920-01-01',
            ],

            'lid_type' => [
                'required',
                'string',
                'in:Passief,Actief,Bijzonder',
            ],

            'lid_sinds' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:1900-01-01',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Naam is verplicht.',
            'name.min'      => 'Naam moet minstens 2 tekens bevatten.',
            'name.max'      => 'Naam mag maximaal 255 tekens bevatten.',
            'name.regex'    => 'Naam mag alleen letters, spaties en streepjes bevatten.',

            'email.required' => 'Email is verplicht.',
            'email.email'    => 'Voer een geldig e-mailadres in (bijv. naam@voorbeeld.com).',
            'email.unique'   => 'Dit e-mailadres is al geregistreerd. Dit lid bestaat mogelijk al.',
            'email.max'      => 'Email mag maximaal 255 tekens bevatten.',

            'telefoonnummer.required' => 'Telefoonnummer is verplicht.',
            'telefoonnummer.min'      => 'Telefoonnummer moet minstens 7 cijfers bevatten.',
            'telefoonnummer.max'      => 'Telefoonnummer mag maximaal 20 tekens bevatten.',
            'telefoonnummer.regex'    => 'Telefoonnummer mag alleen cijfers, +, spaties en streepjes bevatten.',
            'telefoonnummer.unique'   => 'Dit telefoonnummer is al geregistreerd. Dit lid bestaat mogelijk al.',

            'woonplaats.required' => 'Woonplaats is verplicht.',
            'woonplaats.in'       => 'Selecteer een geldige woonplaats uit de lijst.',

            'adres.required' => 'Adres is verplicht.',
            'adres.min'      => 'Adres moet minstens 5 tekens bevatten.',
            'adres.max'      => 'Adres mag maximaal 255 tekens bevatten.',

            'geboortedatum.required' => 'Geboortedatum is verplicht.',
            'geboortedatum.date'     => 'Voer een geldige datum in.',
            'geboortedatum.before'   => 'Geboortedatum moet in het verleden liggen.',
            'geboortedatum.after'    => 'Geboortedatum is ongeldig.',

            'lid_type.required' => 'Lid type is verplicht.',
            'lid_type.in'       => 'Selecteer een geldig lid type uit de lijst.',

            'lid_sinds.required' => 'Lid sinds is verplicht.',
            'lid_sinds.date'     => 'Voer een geldige datum in.',
            'lid_sinds.before_or_equal' => 'Lid sinds mag niet in de toekomst liggen.',
            'lid_sinds.after'    => 'Lid sinds is ongeldig.',
        ];
    }
}
