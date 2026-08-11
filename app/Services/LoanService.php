<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ItemStatus;
use App\Enums\MovementStatus;
use App\Models\MovementItem;
use Exception;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Processa a devolução parcial ou total de um item de empréstimo.
     *
     * @throws Exception
     */
    public function processReturn(MovementItem $item, int $returnQuantity, ?string $notes = null): void
    {
        DB::transaction(function () use ($item, $returnQuantity, $notes) {
            $pending = $item->pendingQuantity();

            if ($returnQuantity <= 0) {
                throw new Exception('A quantidade a devolver deve ser maior que zero.');
            }

            if ($returnQuantity > $pending) {
                throw new Exception("Quantidade a devolver ({$returnQuantity}) é superior à pendente ({$pending}).");
            }

            // Devolve o saldo ao estoque do material
            $item->material->increment('current_stock', $returnQuantity);

            // Atualiza o item da movimentação
            $newReturnedQuantity = $item->returned_quantity + $returnQuantity;
            $isFullyReturned = ($newReturnedQuantity >= $item->quantity);

            $item->update([
                'returned_quantity' => $newReturnedQuantity,
                'status' => $isFullyReturned ? ItemStatus::RETURNED : ItemStatus::PENDING_RETURN,
                'actual_return_date' => $isFullyReturned ? now() : $item->actual_return_date,
            ]);

            // Atualiza o cabeçalho da movimentação
            $movement = $item->movement;
            $allItemsReturned = $movement->items->every(fn (MovementItem $i) => $i->status === ItemStatus::RETURNED || $i->status === ItemStatus::DELIVERED);

            if ($allItemsReturned) {
                $movement->update(['status' => MovementStatus::COMPLETED]);
            } else {
                $movement->update(['status' => MovementStatus::PARTIALLY_RETURNED]);
            }
        });
    }
}
