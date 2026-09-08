<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-materials') ?? false;
    }

    public function rules(): array
    {
        $materialId = $this->route('material')?->id ?? $this->route('material');

        return [
            'code_sku' => ['nullable', 'string', 'max:50', 'unique:materials,code_sku,' . $materialId],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_measure' => ['required', 'string', 'max:10'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'ca_number' => ['nullable', 'string', 'max:50'],
            'ca_validity' => ['nullable', 'date'],
            'expiration_date' => ['nullable', 'date'],
            'patrimony_code' => ['nullable', 'string', 'max:50', 'unique:materials,patrimony_code,' . $materialId],
            'is_returnable' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code_sku' => 'código SKU',
            'name' => 'nome do material',
            'category_id' => 'categoria',
            'unit_measure' => 'unidade de medida',
            'current_stock' => 'estoque inicial',
            'minimum_stock' => 'estoque mínimo',
            'ca_number' => 'número do CA',
            'ca_validity' => 'validade do CA',
            'expiration_date' => 'data de validade',
            'patrimony_code' => 'código de patrimônio',
            'is_returnable' => 'retornável',
            'status' => 'status',
            'image' => 'imagem do material',
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'O arquivo enviado deve ser uma imagem válida.',
            'image.mimes' => 'A imagem deve estar em um dos formatos: JPEG, PNG, JPG, WEBP ou GIF.',
            'image.max' => 'A imagem não pode ultrapassar o tamanho máximo de 5MB.',
        ];
    }
}
