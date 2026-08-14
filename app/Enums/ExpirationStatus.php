<?php

declare(strict_types=1);

namespace App\Enums;

enum ExpirationStatus: string
{
    case EXPIRED = 'EXPIRED';
    case EXPIRING_SOON = 'EXPIRING_SOON';
    case VALID = 'VALID';
    case NONE = 'NONE';

    public function label(): string
    {
        return match($this) {
            self::EXPIRED => 'Vencido',
            self::EXPIRING_SOON => 'Próximo de Vencer',
            self::VALID => 'Válido',
            self::NONE => 'Sem Validade',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::EXPIRED => 'badge bg-danger',
            self::EXPIRING_SOON => 'badge bg-warning text-dark',
            self::VALID => 'badge bg-success',
            self::NONE => 'badge bg-secondary',
        };
    }
}
