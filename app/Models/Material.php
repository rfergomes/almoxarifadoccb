<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ExpirationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_sku',
        'name',
        'category_id',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function movementItems(): HasMany
    {
        return $this->hasMany(MovementItem::class);
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
}

