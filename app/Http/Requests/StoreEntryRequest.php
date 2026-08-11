<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-movements') ?? false;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'string', Rule::enum(DocumentType::class)],
            'document_number' => ['required', 'string', 'max:50'],
            'supplier_or_donor' => ['required', 'string', 'max:150'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'issued_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.material_id' => ['required', 'exists:materials,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type.required' => 'Selecione o tipo de documento de entrada.',
            'document_number.required' => 'Informe o número do documento ou nota fiscal.',
            'supplier_or_donor.required' => 'Informe o fornecedor ou doador.',
            'items.required' => 'Adicione pelo menos um item para dar entrada no estoque.',
            'items.*.quantity.min' => 'A quantidade informada para entrada deve ser de no mínimo 1.',
        ];
    }
}
