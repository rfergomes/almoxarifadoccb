<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ItemStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'movement_id',
        'material_id',
        'quantity',
        'returned_quantity',
        'expected_return_date',
        'actual_return_date',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'returned_quantity' => 'integer',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'status' => ItemStatus::class,
    ];

    public function movement(): BelongsTo
    {
        return $this->belongsTo(Movement::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function pendingQuantity(): int
    {
        return max(0, $this->quantity - $this->returned_quantity);
    }

    public function isOverdue(): bool
    {
        if ($this->status !== ItemStatus::PENDING_RETURN || !$this->expected_return_date) {
            return false;
        }

        return $this->expected_return_date->isPast();
    }
}
