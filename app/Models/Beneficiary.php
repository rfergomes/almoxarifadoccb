<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document_cpf',
        'phone',
        'role_in_ccb',
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
