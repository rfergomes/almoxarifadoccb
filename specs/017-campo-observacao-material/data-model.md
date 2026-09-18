# Data Model: Campo de Observação no Catálogo de Materiais

**Feature**: `017-campo-observacao-material`  
**Date**: 2026-09-18  

---

## 1. Modificações no Esquema do Banco de Dados

### Tabela: `materials`
Adição via migration incremental `add_notes_to_materials_table`.

| Coluna | Tipo | Nulo | Padrão | Descrição |
| :--- | :--- | :---: | :--- | :--- |
| `id` | `bigint unsigned` | Não | Auto | Identificador primário do material |
| `code_sku` | `varchar(50)` | Não | - | Código identificador (ex: `CCB-001`) |
| `name` | `varchar(150)` | Não | - | Nome do material |
| `category_id` | `bigint unsigned` | Não | - | Chave estrangeira para `categories` |
| `image_path` | `varchar(255)` | Sim | `NULL` | Caminho do arquivo de foto no disco público |
| `unit_measure` | `varchar(10)` | Não | `UN` | Unidade de medida |
| `current_stock` | `integer` | Não | `0` | Saldo físico atual em estoque |
| `minimum_stock` | `integer` | Não | `0` | Estoque mínimo de segurança |
| `ca_number` | `varchar(50)` | Sim | `NULL` | Certificado de Aprovação (para EPI) |
| `ca_validity` | `date` | Sim | `NULL` | Validade do CA |
| `expiration_date`| `date` | Sim | `NULL` | Data de validade (perecíveis) |
| `patrimony_code` | `varchar(50)` | Sim | `NULL` | Código patrimonial da entidade |
| `notes` *(NOVA)* | `text` | Sim | `NULL` | Observações técnicas, de manuseio ou fornecedor |
| `is_returnable` | `boolean` | Não | `0` | Indica se o item é retornável |
| `status` | `boolean` | Não | `1` | Status ativo/inativo |
| `created_at` | `timestamp` | Sim | `NULL` | Registro de data de criação |
| `updated_at` | `timestamp` | Sim | `NULL` | Registro de data de atualização |

---

## 2. Entidade Eloquent (`App\Models\Material`)

### Atributos e Métodos Atualizados

- **`$fillable`**:
  Adicionar `'notes'` ao array `$fillable`.
  ```php
  protected $fillable = [
      'code_sku',
      'name',
      'category_id',
      'image_path',
      'unit_measure',
      'current_stock',
      'minimum_stock',
      'ca_number',
      'ca_validity',
      'expiration_date',
      'patrimony_code',
      'notes',
      'is_returnable',
      'status',
  ];
  ```

- **Helper `hasNotes(): bool`**:
  ```php
  public function hasNotes(): bool
  {
      return !empty(trim((string) $this->notes));
  }
  ```

---

## 3. Regras de Validação de Dados

### Cadastro (`StoreMaterialRequest`)
```php
'notes' => ['nullable', 'string', 'max:2000'],
```
Com atributo de localização:
```php
'notes' => 'observações',
```

### Atualização (`MaterialController::update`)
```php
'notes' => ['nullable', 'string', 'max:2000'],
```

---

## 4. Integração nos Fluxos do Sistema

1. **Criação Padrão (`MaterialController::store`)**:
   - O campo `notes` é validado e incluído no array `$data`, sendo persistido diretamente pelo `Material::create($data)`.
2. **Edição Padrão (`MaterialController::update`)**:
   - O campo `notes` é validado, substituído ou limpo caso o usuário deixe em branco, sendo atualizado via `$material->update($data)`.
3. **Criação Rápida (`QuickRegistrationController::material`)**:
   - O formulário rápido na tela de entradas permite inserir `notes`, persistindo junto aos dados básicos e retornando-o na resposta JSON se necessário.
