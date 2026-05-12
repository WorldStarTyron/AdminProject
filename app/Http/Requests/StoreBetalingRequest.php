<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBetalingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lid_id'            => 'required|exists:leden,lid_id',
            'datum'             => 'required|date',
            'bedrag'            => 'required|numeric|min:0',
            'methode'           => 'required|string',
            'status'            => 'required|string',
            'omschrijving'      => 'nullable|string|max:255',
            'bonnummer'         => 'nullable|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'lid_id.required'       => 'Kies een lid.',
            'lid_id.exists'         => 'Geselecteerd lid bestaat niet.',
            'datum.required'        => 'Datum is verplicht.',
            'bedrag.required'       => 'Bedrag is verplicht.',
            'bedrag.min'          => 'Bedrag kan niet negatief zijn.',
            'methode.required'      => 'Betalingsmethode is verplicht.',
            'status.required'       => 'Status is verplicht.',
        ];
    }
}
