<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeneficiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-beneficiaries') ?? false;
    }

    public function rules(): array
    {
        $beneficiaryId = $this->route('beneficiary')?->id ?? $this->route('beneficiary');

        return [
            'name' => ['required', 'string', 'max:150'],
            'document_cpf' => ['nullable', 'string', 'max:14', 'unique:beneficiaries,document_cpf,' . $beneficiaryId],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_in_ccb' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'boolean'],
        ];
    }
}
