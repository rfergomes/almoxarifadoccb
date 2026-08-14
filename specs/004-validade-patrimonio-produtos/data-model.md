# Data Model: Controle de Validade e Patrimônio de Produtos

## Entity Blueprint & Schema Updates

### 1. `materials` Table (Migration Update)

#### New Columns
| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `expiration_date` | `DATE` | Yes | `NULL` | Data de validade do produto/insumo (tintas, massas, etc.) |
| `patrimony_code` | `VARCHAR(50)` | Yes | `NULL` | Código/Número de patrimônio da entidade (equipamentos, ferramentas) |

#### Indexes & Constraints
- `UNIQUE INDEX materials_patrimony_code_unique (patrimony_code)`
- `INDEX materials_expiration_date_index (expiration_date)`

---

### 2. Enum: `App\Enums\ExpirationStatus`

```php
declare(strict_types=1);

namespace App\Enums;

enum ExpirationStatus: string
{
    case EXPIRED = 'EXPIRED';
    case EXPIRING_SOON = 'EXPIRING_SOON';
    case VALID = 'VALID';
    case NONE = 'NONE';

    public function label(): string
    {
        return match($this) {
            self::EXPIRED => 'Vencido',
            self::EXPIRING_SOON => 'Próximo de Vencer',
            self::VALID => 'Válido',
            self::NONE => 'Sem Validade',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::EXPIRED => 'badge bg-danger',
            self::EXPIRING_SOON => 'badge bg-warning text-dark',
            self::VALID => 'badge bg-success',
            self::NONE => 'badge bg-secondary',
        };
    }
}
```

---

### 3. Model Updates (`App\Models\Material`)

#### Attributes & Casts
- `expiration_date` cast as `'date'`
- `patrimony_code` cast as `'string'`

#### Domain Helper Methods
- `expirationStatus(int $daysThreshold = 30): ExpirationStatus`
- `isExpired(): bool`
- `isExpiringSoon(int $daysThreshold = 30): bool`
- `hasPatrimony(): bool`

#### Scopes
- `scopeExpired($query)`
- `scopeExpiringSoon($query, int $daysThreshold = 30)`
- `scopeWithPatrimony($query)`
- `scopeSearchPatrimony($query, string $code)`

---

### 4. Validation Rules (`FormRequest` classes)

#### `MaterialRequest` / Update Material
- `expiration_date`: `['nullable', 'date']`
- `patrimony_code`: `['nullable', 'string', 'max:50', 'unique:materials,patrimony_code,' . $materialId]`
