<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\MovementType;
use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-movements') ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::enum(MovementType::class)],
            'beneficiary_id' => ['required', 'exists:beneficiaries,id'],
            'destination_id' => ['required', 'exists:destinations,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.material_id' => ['required', 'exists:materials,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.expected_return_date' => ['required_if:type,LOAN', 'nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (is_array($this->items)) {
                foreach ($this->items as $index => $item) {
                    if (isset($item['material_id'])) {
                        $material = Material::find($item['material_id']);
                        if (! $material) {
                            continue;
                        }

                        // 1. Regra para Modalidade Empréstimo: bloqueia materiais não retornáveis (consumo)
                        if ($this->type === MovementType::LOAN->value && ! $material->is_returnable) {
                            $validator->errors()->add("items.{$index}.material_id", "O material '{$material->name}' é de consumo descartável e não pode ser lançado na modalidade Empréstimo.");
                        }

                        // 2. Regra para Modalidade Consumo Geral: bloqueia equipamentos e ferramentas retornáveis
                        if ($this->type === MovementType::CONSUMPTION->value && $material->is_returnable) {
                            $validator->errors()->add("items.{$index}.material_id", "O material '{$material->name}' é um equipamento retornável e não pode ser baixado em Consumo Geral. Utilizar a modalidade Empréstimo.");
                        }

                        // 3. Regra para Modalidade Entrega de EPI: bloqueia materiais que não são EPI
                        if ($this->type === MovementType::EPI->value) {
                            if (! $material->isEpi()) {
                                $validator->errors()->add("items.{$index}.material_id", "O material '{$material->name}' não pertence à categoria de EPIs e não pode ser lançado na modalidade Entrega de EPI.");
                            } else {
                                if (empty($material->ca_number)) {
                                    $validator->errors()->add("items.{$index}.material_id", "O EPI '{$material->name}' necessita de um número de CA válido no cadastro.");
                                }
                                if ($material->isCaExpired()) {
                                    $validator->errors()->add("items.{$index}.material_id", "O EPI '{$material->name}' está com o Certificado de Aprovação (CA) vencido.");
                                }
                            }
                        }
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'type.required' => 'O tipo de movimentação é obrigatório.',
            'beneficiary_id.required' => 'Selecione o beneficiário responsável pela retirada.',
            'destination_id.required' => 'Selecione o destino de aplicação dos materiais.',
            'items.required' => 'Adicione pelo menos um item à movimentação.',
            'items.*.quantity.min' => 'A quantidade informada deve ser de no mínimo 1.',
            'items.*.expected_return_date.required_if' => 'Para empréstimos, informe a data prevista de devolução.',
        ];
    }
}
