<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-destinations') ?? false;
    }

    public function rules(): array
    {
        $destinationId = $this->route('destination')?->id ?? $this->route('destination');

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:30', 'unique:destinations,code,' . $destinationId],
            'type' => ['required', 'in:casa_de_oracao,obra,administracao,outro'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
        ];
    }
}
