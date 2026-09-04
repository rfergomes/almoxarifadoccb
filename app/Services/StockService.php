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
    public function __construct(
        protected ?AttachmentService $attachmentService = null
    ) {
        $this->attachmentService = $attachmentService ?? app(AttachmentService::class);
    }

    /**
     * Exclui uma movimentação e reverte o impacto em estoque dentro de uma transação atômica.
     *
     * @throws Exception
     */
    public function deleteMovement(Movement $movement): void
    {
        DB::transaction(function () use ($movement) {
            $movement->load(['items.material', 'entryDocument.attachments', 'attachments']);

            if ($movement->type === MovementType::ENTRY) {
                // 1. Entrada: verifica se o saldo de cada material é suficiente para subtrair
                foreach ($movement->items as $item) {
                    $material = $item->material;
                    if ($material->current_stock < $item->quantity) {
                        throw new Exception("Não é possível excluir esta entrada pois causaria saldo negativo no material '{$material->name}'. Estoque atual: {$material->current_stock} {$material->unit_measure}, quantidade da entrada: {$item->quantity} {$material->unit_measure}.");
                    }
                }

                // Subtrai o estoque adicionado pela entrada
                foreach ($movement->items as $item) {
                    $item->material->decrement('current_stock', $item->quantity);
                }

                // Remove documentos e anexos de entrada
                if ($movement->entryDocument) {
                    foreach ($movement->entryDocument->attachments as $att) {
                        $this->attachmentService->deleteAttachment($att);
                    }
                    $movement->entryDocument->delete();
                }
            } elseif ($movement->type === MovementType::LOAN) {
                // Empréstimo: estorna os itens que ainda não haviam sido devolvidos
                foreach ($movement->items as $item) {
                    $pending = $item->quantity - $item->returned_quantity;
                    if ($pending > 0) {
                        $item->material->increment('current_stock', $pending);
                    }
                }
            } else {
                // CONSUMPTION e EPI: os itens saíram do estoque, então estornamos somando de volta
                foreach ($movement->items as $item) {
                    $item->material->increment('current_stock', $item->quantity);
                }
            }

            // Exclui anexos diretamente vinculados à movimentação
            foreach ($movement->attachments as $att) {
                $this->attachmentService->deleteAttachment($att);
            }

            // Exclui os itens da movimentação e o cabeçalho
            $movement->items()->delete();
            $movement->delete();
        });
    }

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

    public function getExpiredMaterialsCount(): int
    {
        return Material::expired()->count();
    }

    public function getExpiringSoonMaterialsCount(int $daysThreshold = 30): int
    {
        return Material::expiringSoon($daysThreshold)->count();
    }

    public function getPatrimonyMaterialsCount(): int
    {
        return Material::withPatrimony()->count();
    }

    public function getExpiredMaterials(): \Illuminate\Database\Eloquent\Collection
    {
        return Material::expired()->with('category')->get();
    }

    public function getExpiringSoonMaterials(int $daysThreshold = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Material::expiringSoon($daysThreshold)->with('category')->get();
    }
}

