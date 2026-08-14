<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class EntryDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_type',
        'supplier_or_donor',
        'total_amount',
        'issued_at',
        'notes',
    ];

    protected $casts = [
        'document_type' => DocumentType::class,
        'total_amount' => 'decimal:2',
        'issued_at' => 'date',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable')->latestOfMany();
    }
}
