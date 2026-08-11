<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ItemStatus;
use App\Enums\MovementStatus;
use App\Enums\MovementType;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockService
{
    /**
     * Lança uma movimentação de saída (Consumo, EPI ou Empréstimo) dentro de uma transação SQL.
     *
     * @throws Exception
     */
    public function createMovement(array $data, int $userId): Movement
    {
        return DB::transaction(function () use ($data, $userId) {
            $type = MovementType::from($data['type']);
            $movementStatus = match ($type) {
                MovementType::CONSUMPTION, MovementType::EPI => MovementStatus::COMPLETED,
                MovementType::LOAN => MovementStatus::OPEN,
            };

            // Gera código único para a movimentação
            $code = 'MOV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $movement = Movement::create([
                'code' => $code,
                'user_id' => $userId,
                'beneficiary_id' => $data['beneficiary_id'],
                'destination_id' => $data['destination_id'],
                'type' => $type,
                'status' => $movementStatus,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $itemData) {
                /** @var Material $material */
                $material = Material::findOrFail($itemData['material_id']);
                $requestedQuantity = (int) $itemData['quantity'];

                // Validação estrita de saldo de estoque
                if ($material->current_stock < $requestedQuantity) {
                    throw new Exception("Saldo insuficiente para o material '{$material->name}'. Estoque disponível: {$material->current_stock} {$material->unit_measure}.");
                }

                // Decrementa o saldo do material
                $material->decrement('current_stock', $requestedQuantity);

                $itemStatus = match ($type) {
                    MovementType::CONSUMPTION, MovementType::EPI => ItemStatus::DELIVERED,
                    MovementType::LOAN => ItemStatus::PENDING_RETURN,
                };

                MovementItem::create([
                    'movement_id' => $movement->id,
                    'material_id' => $material->id,
                    'quantity' => $requestedQuantity,
                    'returned_quantity' => 0,
                    'expected_return_date' => $itemData['expected_return_date'] ?? null,
                    'status' => $itemStatus,
                ]);
            }

            return $movement;
        });
    }
}
