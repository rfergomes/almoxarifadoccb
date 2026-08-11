<?php

declare(strict_types=1);

namespace App\Enums;

enum MovementType: string
{
    case CONSUMPTION = 'CONSUMPTION';
    case EPI = 'EPI';
    case LOAN = 'LOAN';
    case ENTRY = 'ENTRY';

    public function label(): string
    {
        return match ($this) {
            self::CONSUMPTION => 'Consumo Geral',
            self::EPI => 'Entrega de EPI',
            self::LOAN => 'Empréstimo de Ferramenta/Equipamento',
            self::ENTRY => 'Entrada de Estoque',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::CONSUMPTION => 'bg-info',
            self::EPI => 'bg-warning text-dark',
            self::LOAN => 'bg-primary',
            self::ENTRY => 'bg-success',
        };
    }
}
