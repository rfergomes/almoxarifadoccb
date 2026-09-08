<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'avatar_path',
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
        if ($this->relationLoaded('roles')) {
            $roleNames = $this->roles->pluck('name')->all();
            if (in_array('Administrador', $roleNames, true)) {
                return 'Administrador';
            }
            if (in_array('Almoxarife', $roleNames, true)) {
                return 'Almoxarife';
            }
            if (in_array('Consulta', $roleNames, true)) {
                return 'Consulta';
            }
            return $roleNames[0] ?? 'Consulta';
        }

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

    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function hasAvatar(): bool
    {
        return !empty($this->avatar_path) && Storage::disk('public')->exists($this->avatar_path);
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name));
        if (empty($words) || empty($words[0])) {
            return 'U';
        }
        if (count($words) === 1) {
            return mb_strtoupper(mb_substr($words[0], 0, 2));
        }
        return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1));
    }

    /**
     * Retorna apenas o primeiro nome do usuário (ex: "Rodrigo").
     */
    public function getFirstNameAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name));
        return !empty($words[0]) ? $words[0] : (string) $this->name;
    }

    /**
     * Retorna o primeiro e o último nome do usuário (ex: "Rodrigo Lima").
     * Para monônimos (ex: "Almoxarife"), retorna o próprio nome.
     */
    public function getShortNameAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name));
        if (empty($words) || empty($words[0])) {
            return (string) $this->name;
        }

        if (count($words) === 1) {
            return $words[0];
        }

        return $words[0] . ' ' . end($words);
    }
}
