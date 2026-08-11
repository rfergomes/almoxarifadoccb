<?php

declare(strict_types=1);

namespace App\Models;

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
        'is_returnable',
        'status',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'ca_validity' => 'date',
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
}
