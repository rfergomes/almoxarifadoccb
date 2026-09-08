<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ExpirationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    use HasFactory;

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
        'is_returnable',
        'status',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'ca_validity' => 'date',
        'expiration_date' => 'date',
        'is_returnable' => 'boolean',
        'status' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Material $material): void {
            if (empty(trim((string) $material->code_sku))) {
                $material->code_sku = static::generateNextSku();
            }
        });
    }

    /**
     * Gera o próximo código sequencial de SKU no formato CCB-### (ex: CCB-001, CCB-002, etc.).
     */
    public static function generateNextSku(): string
    {
        $existingSkus = static::where('code_sku', 'LIKE', 'CCB-%')->pluck('code_sku');

        $maxNumber = 0;
        foreach ($existingSkus as $sku) {
            if (is_string($sku) && preg_match('/^CCB-(\d+)$/', $sku, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = $maxNumber + 1;
        $padded = str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

        return "CCB-{$padded}";
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function movementItems(): HasMany
    {
        return $this->hasMany(MovementItem::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function isEpi(): bool
    {
        return $this->category && strtoupper($this->category->name) === 'EPI';
    }

    public function isCaExpired(): bool
    {
        if (!$this->ca_validity) {
            return false;
        }

        return $this->ca_validity->isPast();
    }

    public function isStockLow(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function expirationStatus(int $daysThreshold = 30): ExpirationStatus
    {
        if (!$this->expiration_date) {
            return ExpirationStatus::NONE;
        }

        if ($this->isExpired()) {
            return ExpirationStatus::EXPIRED;
        }

        $days = (int) now()->startOfDay()->diffInDays($this->expiration_date->startOfDay(), false);
        if ($days >= 0 && $days <= $daysThreshold) {
            return ExpirationStatus::EXPIRING_SOON;
        }

        return ExpirationStatus::VALID;
    }

    public function isExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->isPast() && !$this->expiration_date->isToday();
    }

    public function isExpiringSoon(int $daysThreshold = 30): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        $days = now()->startOfDay()->diffInDays($this->expiration_date->startOfDay(), false);
        return $days >= 0 && $days <= $daysThreshold;
    }

    public function hasPatrimony(): bool
    {
        return !empty($this->patrimony_code);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expiration_date')
            ->where('expiration_date', '<', now()->startOfDay());
    }

    public function scopeExpiringSoon(Builder $query, int $daysThreshold = 30): Builder
    {
        return $query->whereNotNull('expiration_date')
            ->where('expiration_date', '>=', now()->startOfDay())
            ->where('expiration_date', '<=', now()->addDays($daysThreshold)->endOfDay());
    }

    public function scopeWithPatrimony(Builder $query): Builder
    {
        return $query->whereNotNull('patrimony_code')
            ->where('patrimony_code', '!=', '');
    }

    public function scopeSearchPatrimony(Builder $query, string $code): Builder
    {
        return $query->where('patrimony_code', 'like', "%{$code}%");
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function hasImage(): bool
    {
        return !empty($this->image_path) && Storage::disk('public')->exists($this->image_path);
    }
}

