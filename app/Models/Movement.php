<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MovementStatus;
use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'beneficiary_id',
        'destination_id',
        'entry_document_id',
        'type',
        'status',
        'notes',
    ];

    protected $casts = [
        'type' => MovementType::class,
        'status' => MovementStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function entryDocument(): BelongsTo
    {
        return $this->belongsTo(EntryDocument::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MovementItem::class);
    }
}
