# Data Model: Inclusão e Gestão de Imagem no Catálogo de Materiais

**Feature**: `014-imagem-produto-material`  
**Date**: 2026-09-08  

---

## 1. Modificações no Banco de Dados

### Tabela: `materials`
Alteração via migration incremental `add_image_path_to_materials_table`.

| Coluna | Tipo | Nulo | Padrão | Descrição |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | Não | Auto | Identificador primário do material |
| `code_sku` | `varchar(50)` | Não | - | Código identificador (ex: `CCB-001`) |
| `name` | `varchar(150)` | Não | - | Nome e descrição resumida do material |
| `category_id` | `bigint unsigned` | Não | - | Chave estrangeira para `categories` |
| `image_path` *(NOVA)* | `varchar(255)` | Sim | `NULL` | Caminho do arquivo de imagem no disco `public` (ex: `materials/images/abc-123.jpg`) |
| `unit_measure` | `varchar(10)` | Não | `UN` | Unidade de medida |
| `current_stock` | `integer` | Não | `0` | Saldo físico atual em estoque |
| `minimum_stock` | `integer` | Não | `0` | Ponto de pedido / estoque mínimo |
| `ca_number` | `varchar(50)` | Sim | `NULL` | Certificado de Aprovação (para EPI) |
| `ca_validity` | `date` | Sim | `NULL` | Validade do CA |
| `expiration_date`| `date` | Sim | `NULL` | Data de validade para perecíveis |
| `patrimony_code` | `varchar(50)` | Sim | `NULL` | Código patrimonial para bens duráveis |
| `is_returnable` | `boolean` | Não | `0` | Indica se o item é retornável (ferramenta) |
| `status` | `boolean` | Não | `1` | Status ativo/inativo |
| `created_at` | `timestamp` | Sim | `NULL` | Data de criação |
| `updated_at` | `timestamp` | Sim | `NULL` | Data de atualização |

---

## 2. Entidade Eloquent (`App\Models\Material`)

### Novos Atributos e Métodos

- **`$fillable`**:
  Incluir `'image_path'` na lista de atributos preenchíveis.
- **Accessor `imageUrl` / `getImageUrlAttribute()`**:
  ```php
  public function getImageUrlAttribute(): ?string
  {
      if (!$this->image_path) {
          return null;
      }
      return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path);
  }
  ```
- **Helper `hasImage(): bool`**:
  ```php
  public function hasImage(): bool
  {
      return !empty($this->image_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image_path);
  }
  ```

---

## 3. Regras de Validação de Dados

### Cadastro (`StoreMaterialRequest`)
```php
'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
```

### Atualização (`UpdateMaterialRequest` ou validação em `MaterialController::update`)
```php
'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
'remove_image' => ['nullable', 'boolean'],
```

---

## 4. Ciclo de Vida do Arquivo Físico

1. **Criação com Imagem**:
   - Arquivo enviado via formulário multipart.
   - `MaterialImageService` gera nome UUID e salva em `storage/app/public/materials/images/`.
   - Coluna `image_path` recebe o caminho relativo.
2. **Atualização**:
   - Se `remove_image == true`: `MaterialImageService` remove o arquivo anterior e define `image_path = null`.
   - Se novo arquivo `image` for enviado: `MaterialImageService` remove o arquivo antigo do disco e armazena o novo.
   - Se nenhum arquivo enviado e `remove_image` for falso: mantém `image_path` inalterado.
3. **Exclusão do Material**:
   - Ao executar `Material::destroy()`, o arquivo de imagem associado em `image_path` é excluído do disco `public`.
