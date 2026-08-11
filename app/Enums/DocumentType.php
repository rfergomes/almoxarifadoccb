<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentType: string
{
    case NOTA_FISCAL = 'NOTA_FISCAL';
    case DOACAO = 'DOACAO';
    case COMPRA_DIRETA = 'COMPRA_DIRETA';
    case OUTRO = 'OUTRO';

    public function label(): string
    {
        return match ($this) {
            self::NOTA_FISCAL => 'Nota Fiscal',
            self::DOACAO => 'Termo de Doação',
            self::COMPRA_DIRETA => 'Compra Direta / Recibo',
            self::OUTRO => 'Outro Documento',
        };
    }
}
