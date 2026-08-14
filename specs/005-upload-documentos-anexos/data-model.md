# Data Model: Upload de Arquivos e Anexos em Entradas e Avarias

## Entity Blueprint & Schema Updates

### 1. `attachments` Table (New Table Migration)

#### Schema
| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | `BIGINT UNSIGNED` | No | AUTO_INC | Chave primária |
| `attachable_type` | `VARCHAR(150)` | No | - | Nome da classe do modelo relacionado (Morph) |
| `attachable_id` | `BIGINT UNSIGNED` | No | - | ID do modelo relacionado (Morph) |
| `file_path` | `VARCHAR(255)` | No | - | Caminho relativo do arquivo no disco (`storage`) |
| `original_name` | `VARCHAR(255)` | No | - | Nome original do arquivo enviado pelo usuário |
| `mime_type` | `VARCHAR(100)` | No | - | Tipo MIME do arquivo (ex.: `application/pdf`, `image/png`) |
| `file_size` | `BIGINT UNSIGNED` | No | - | Tamanho do arquivo em bytes |
| `uploaded_by` | `FOREIGN ID` | No | - | Referência ao usuário que enviou o arquivo (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | Data de upload |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | Data de atualização |

#### Indexes & Constraints
- `INDEX attachments_attachable_type_attachable_id_index (attachable_type, attachable_id)`
- `FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT`

---

### 2. Model: `App\Models\Attachment`

```php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function formattedSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 2) . ' KB';
    }
}
```

---

### 3. Model Traits / Polymorphic Relationships

#### `EntryDocument` & `Movement` Updates
- `public function attachments(): MorphMany`
- `public function attachment(): MorphOne` (retorna o anexo principal/mais recente)

---

### 4. Validation Rules (`FormRequest` classes)

#### `StoreEntryRequest` & `StoreMovementRequest`
- `document_file`: `['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240']`
