# Data Model: Foto de Perfil do Usuário e Perfil Pessoal

**Feature**: `015-foto-perfil-usuario`  
**Date**: 2026-09-08  

---

## 1. Modificações no Banco de Dados

### Tabela: `users`
Alteração via migration incremental `add_avatar_path_to_users_table`.

| Coluna | Tipo | Nulo | Padrão | Descrição |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | Não | Auto | Identificador primário do usuário |
| `name` | `varchar(255)` | Não | - | Nome completo do usuário |
| `email` | `varchar(255)` | Não | - | E-mail corporativo / login (único) |
| `avatar_path` *(NOVA)* | `varchar(255)` | Sim | `NULL` | Caminho do arquivo de foto no disco `public` (ex: `avatars/abc-123.jpg`) |
| `password` | `varchar(255)` | Não | - | Senha hashada do usuário |
| `status` | `boolean` | Não | `1` | Usuário ativo/inativo |
| `created_at` | `timestamp` | Sim | `NULL` | Data de cadastro |
| `updated_at` | `timestamp` | Sim | `NULL` | Data da última atualização |

---

## 2. Entidade Eloquent (`App\Models\User`)

### Novos Atributos e Métodos

- **`$fillable`**:
  Adicionar `'avatar_path'` à lista de atributos preenchíveis.
- **Accessor `avatarUrl` / `getAvatarUrlAttribute()`**:
  ```php
  public function getAvatarUrlAttribute(): ?string
  {
      if (empty($this->avatar_path)) {
          return null;
      }
      return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar_path);
  }
  ```
- **Helper `hasAvatar(): bool`**:
  ```php
  public function hasAvatar(): bool
  {
      return !empty($this->avatar_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar_path);
  }
  ```
- **Helper `initials(): string`**:
  ```php
  public function initials(): string
  {
      $words = preg_split('/\s+/', trim($this->name));
      if (empty($words)) {
          return 'U';
      }
      if (count($words) === 1) {
          return mb_strtoupper(mb_substr($words[0], 0, 2));
      }
      return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1));
  }
  ```

---

## 3. Regras de Validação de Dados

### Atualização do Perfil (`ProfileUpdateRequest` ou validação do controller)
```php
'name' => ['required', 'string', 'max:150'],
'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
'remove_avatar' => ['nullable', 'boolean'],
```

### Atualização de Senha (`ProfilePasswordUpdateRequest` ou validação do controller)
```php
'current_password' => ['required', 'current_password'],
'password' => ['required', 'string', 'min:8', 'confirmed'],
```

---

## 4. Ciclo de Vida do Arquivo Físico

1. **Upload Inicial ou Troca de Avatar**:
   - Arquivo enviado via formulário multipart.
   - `UserAvatarService` gera nome UUID e armazena em `storage/app/public/avatars/`.
   - Se já existia um avatar anterior, ele é apagado imediatamente do disco.
   - `users.avatar_path` é atualizado.
2. **Remoção de Avatar**:
   - Usuário ou administrador seleciona a opção "Remover Foto".
   - `UserAvatarService` apaga o arquivo físico e define `avatar_path = null`.
3. **Exclusão de Usuário**:
   - Ao excluir um usuário do sistema, o avatar físico associado é deletado do disco.
