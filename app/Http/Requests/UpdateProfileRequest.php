<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome completo',
            'avatar' => 'foto de perfil',
            'remove_avatar' => 'remover foto',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome completo é obrigatório.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'avatar.image' => 'O arquivo enviado deve ser uma imagem válida.',
            'avatar.mimes' => 'A foto de perfil deve estar em formato JPG, PNG, WEBP ou GIF.',
            'avatar.max' => 'A foto de perfil não pode ultrapassar 5MB.',
        ];
    }
}
