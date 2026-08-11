<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'city',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }
}
