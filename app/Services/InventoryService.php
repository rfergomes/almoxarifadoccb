<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    public function startInventory(string $title, int $userId, ?string $notes = null): Inventory
    {
        return DB::transaction(function () use ($title, $userId, $notes) {
            $materials = Material::where('status', true)->orderBy('name')->get();
            $code = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $inventory = Inventory::create([
                'code' => $code,
                'user_id' => $userId,
                'title' => $title,
                'status' => 'OPEN',
                'total_items' => $materials->count(),
                'items_adjusted' => 0,
                'started_at' => now(),
                'notes' => $notes,
            ]);

            foreach ($materials as $material) {
                InventoryItem::create([
                    'inventory_id' => $inventory->id,
                    'material_id' => $material->id,
                    'system_stock' => $material->current_stock,
                    'counted_stock' => null,
                    'difference' => null,
                ]);
            }

            return $inventory;
        });
    }

    public function saveCounts(Inventory $inventory, array $itemsData): void
    {
        DB::transaction(function () use ($itemsData) {
            foreach ($itemsData as $itemId => $data) {
                $item = InventoryItem::find($itemId);
                if (! $item) {
                    continue;
                }

                if (isset($data['counted_stock']) && $data['counted_stock'] !== '' && $data['counted_stock'] !== null) {
                    $counted = (int) $data['counted_stock'];
                    $difference = $counted - $item->system_stock;

                    $item->update([
                        'counted_stock' => $counted,
                        'difference' => $difference,
                        'notes' => $data['notes'] ?? null,
                    ]);
                }
            }
        });
    }

    public function completeInventory(Inventory $inventory): Inventory
    {
        return DB::transaction(function () use ($inventory) {
            $adjustedCount = 0;

            // Busca os itens atualizados no banco de dados
            $items = $inventory->items()->with('material')->get();

            foreach ($items as $item) {
                if ($item->counted_stock !== null) {
                    // Atualiza o saldo do material no estoque para a contagem física real
                    $item->material->update([
                        'current_stock' => $item->counted_stock,
                    ]);

                    if ($item->difference !== 0) {
                        $adjustedCount++;
                    }
                }
            }

            $inventory->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
                'items_adjusted' => $adjustedCount,
            ]);

            return $inventory->fresh();
        });
    }
}
