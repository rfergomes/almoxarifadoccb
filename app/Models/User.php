<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /**
     * Retorna o perfil principal unificado do usuário de acordo com a hierarquia:
     * Administrador > Almoxarife > Consulta.
     */
    public function getPrimaryRoleAttribute(): string
    {
        if ($this->hasRole('Administrador')) {
            return 'Administrador';
        }
        if ($this->hasRole('Almoxarife')) {
            return 'Almoxarife';
        }
        if ($this->hasRole('Consulta')) {
            return 'Consulta';
        }
        return $this->roles->first()?->name ?? 'Consulta';
    }

    /**
     * Retorna a cor do badge correspondente ao perfil principal.
     */
    public function getPrimaryRoleBadgeAttribute(): string
    {
        return match ($this->primary_role) {
            'Administrador' => 'danger',
            'Almoxarife' => 'primary',
            'Consulta' => 'secondary',
            default => 'dark',
        };
    }

    /**
     * Sanitiza os papéis do usuário para garantir perfil único (1:1).
     */
    public function sanitizeRoles(): void
    {
        $roleCount = $this->roles()->count();
        if ($roleCount > 1) {
            $this->syncRoles([$this->primary_role]);
        }
    }

    /**
     * Sanitiza todos os usuários do sistema, garantindo no máximo 1 perfil por usuário.
     */
    public static function sanitizeAllUserRoles(): int
    {
        $count = 0;
        foreach (static::with('roles')->get() as $user) {
            if ($user->roles->count() > 1) {
                $user->syncRoles([$user->primary_role]);
                $count++;
            }
        }
        return $count;
    }
}
