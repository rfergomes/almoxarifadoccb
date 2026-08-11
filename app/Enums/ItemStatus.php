<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemStatus: string
{
    case DELIVERED = 'DELIVERED';
    case PENDING_RETURN = 'PENDING_RETURN';
    case RETURNED = 'RETURNED';

    public function label(): string
    {
        return match ($this) {
            self::DELIVERED => 'Entregue',
            self::PENDING_RETURN => 'Pendente de Devolução',
            self::RETURNED => 'Devolvido',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DELIVERED => 'bg-success',
            self::PENDING_RETURN => 'bg-warning text-dark',
            self::RETURNED => 'bg-primary',
        };
    }
}
