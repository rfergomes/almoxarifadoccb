<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Material;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodicInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Material $material;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();

        $category = Category::create(['name' => 'Elétrica']);
        $this->material = Material::create([
            'code_sku' => 'ELE-001',
            'name' => 'Cabo Flexível 2.5mm',
            'category_id' => $category->id,
            'unit_measure' => 'M',
            'current_stock' => 100,
            'minimum_stock' => 20,
            'status' => true,
        ]);
    }

    public function test_start_inventory_session_creates_records_for_all_materials(): void
    {
        $inventoryService = app(InventoryService::class);

        $inventory = $inventoryService->startInventory(
            'Inventário Geral 3º Trimestre 2026',
            $this->adminUser->id,
            'Contagem de rotina'
        );

        $this->assertEquals('OPEN', $inventory->status);
        $this->assertEquals(1, $inventory->total_items);
        $this->assertEquals(100, $inventory->items->first()->system_stock);
    }

    public function test_save_counts_calculates_difference(): void
    {
        $inventoryService = app(InventoryService::class);
        $inventory = $inventoryService->startInventory('Inventário Teste', $this->adminUser->id);
        $item = $inventory->items->first();

        $inventoryService->saveCounts($inventory, [
            $item->id => [
                'counted_stock' => 105, // Sobra de 5 metros
                'notes' => 'Sobra encontrada na prateleira B',
            ],
        ]);

        $this->assertEquals(105, $item->fresh()->counted_stock);
        $this->assertEquals(5, $item->fresh()->difference);
    }

    public function test_complete_inventory_updates_material_stock_atomically(): void
    {
        $inventoryService = app(InventoryService::class);
        $inventory = $inventoryService->startInventory('Inventário Teste Fechamento', $this->adminUser->id);
        $item = $inventory->items->first();

        $inventoryService->saveCounts($inventory, [
            $item->id => [
                'counted_stock' => 90, // Falta de 10 metros
                'notes' => 'Avaria descartada',
            ],
        ]);

        $inventoryService->completeInventory($inventory);

        $this->assertEquals('COMPLETED', $inventory->fresh()->status);
        $this->assertEquals(90, $this->material->fresh()->current_stock);
        $this->assertEquals(1, $inventory->fresh()->items_adjusted);
    }

    public function test_inventory_pdf_export_returns_ok(): void
    {
        $inventoryService = app(InventoryService::class);
        $inventory = $inventoryService->startInventory('Inventário PDF Teste', $this->adminUser->id);

        $response = $this->actingAs($this->adminUser)
            ->get(route('inventories.pdf', $inventory));

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }
}
