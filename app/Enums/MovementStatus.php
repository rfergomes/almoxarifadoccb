<?php

declare(strict_types=1);

namespace App\Enums;

enum MovementStatus: string
{
    case OPEN = 'OPEN';
    case COMPLETED = 'COMPLETED';
    case PARTIALLY_RETURNED = 'PARTIALLY_RETURNED';
    case OVERDUE = 'OVERDUE';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Em Aberto',
            self::COMPLETED => 'Concluído',
            self::PARTIALLY_RETURNED => 'Devolução Parcial',
            self::OVERDUE => 'Em Atraso',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OPEN => 'bg-secondary',
            self::COMPLETED => 'bg-success',
            self::PARTIALLY_RETURNED => 'bg-info',
            self::OVERDUE => 'bg-danger',
        };
    }
}
