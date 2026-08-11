<?php

namespace Tests\Feature;

use App\Enums\ItemStatus;
use App\Enums\MovementStatus;
use App\Enums\MovementType;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Material;
use App\Models\MovementItem;
use App\Models\User;
use App\Services\LoanService;
use App\Services\StockService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $consultaUser;
    protected Material $materialConsumo;
    protected Material $materialFerramenta;
    protected Destination $destination;
    protected Beneficiary $beneficiary;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();
        $this->consultaUser = User::where('email', 'consulta@ccb.org.br')->first();

        $catConsumo = Category::create(['name' => 'Consumo']);
        $catFerramenta = Category::create(['name' => 'Ferramenta/Equipamento']);

        $this->materialConsumo = Material::create([
            'code_sku' => 'TEST-001',
            'name' => 'Cimento Teste 50kg',
            'category_id' => $catConsumo->id,
            'unit_measure' => 'CX',
            'current_stock' => 20,
            'minimum_stock' => 5,
            'is_returnable' => false,
        ]);

        $this->materialFerramenta = Material::create([
            'code_sku' => 'TEST-002',
            'name' => 'Furadeira Teste',
            'category_id' => $catFerramenta->id,
            'unit_measure' => 'UN',
            'current_stock' => 3,
            'minimum_stock' => 1,
            'is_returnable' => true,
        ]);

        $this->destination = Destination::create([
            'code' => 'CO-TEST',
            'name' => 'C.O. Teste',
            'type' => 'casa_de_oracao',
        ]);

        $this->beneficiary = Beneficiary::create([
            'name' => 'Irmão Teste',
            'role_in_ccb' => 'Voluntário',
        ]);
    }

    public function test_consumption_movement_deducts_stock_immediately(): void
    {
        $stockService = app(StockService::class);

        $data = [
            'type' => MovementType::CONSUMPTION->value,
            'beneficiary_id' => $this->beneficiary->id,
            'destination_id' => $this->destination->id,
            'notes' => 'Teste de consumo',
            'items' => [
                [
                    'material_id' => $this->materialConsumo->id,
                    'quantity' => 5,
                ],
            ],
        ];

        $movement = $stockService->createMovement($data, $this->adminUser->id);

        $this->assertEquals(MovementStatus::COMPLETED, $movement->status);
        $this->assertEquals(15, $this->materialConsumo->fresh()->current_stock);
    }

    public function test_cannot_create_movement_if_quantity_exceeds_stock(): void
    {
        $stockService = app(StockService::class);

        $data = [
            'type' => MovementType::CONSUMPTION->value,
            'beneficiary_id' => $this->beneficiary->id,
            'destination_id' => $this->destination->id,
            'items' => [
                [
                    'material_id' => $this->materialConsumo->id,
                    'quantity' => 100, // Maior que 20
                ],
            ],
        ];

        $this->expectException(Exception::class);
        $stockService->createMovement($data, $this->adminUser->id);
    }

    public function test_loan_movement_creates_pending_return_item(): void
    {
        $stockService = app(StockService::class);

        $data = [
            'type' => MovementType::LOAN->value,
            'beneficiary_id' => $this->beneficiary->id,
            'destination_id' => $this->destination->id,
            'items' => [
                [
                    'material_id' => $this->materialFerramenta->id,
                    'quantity' => 2,
                    'expected_return_date' => now()->addDays(5)->format('Y-m-d'),
                ],
            ],
        ];

        $movement = $stockService->createMovement($data, $this->adminUser->id);

        $this->assertEquals(MovementStatus::OPEN, $movement->status);
        $this->assertEquals(1, $this->materialFerramenta->fresh()->current_stock);
        $this->assertEquals(ItemStatus::PENDING_RETURN, $movement->items->first()->status);
    }

    public function test_return_item_restores_stock_and_updates_status(): void
    {
        $stockService = app(StockService::class);
        $loanService = app(LoanService::class);

        $movement = $stockService->createMovement([
            'type' => MovementType::LOAN->value,
            'beneficiary_id' => $this->beneficiary->id,
            'destination_id' => $this->destination->id,
            'items' => [
                [
                    'material_id' => $this->materialFerramenta->id,
                    'quantity' => 2,
                    'expected_return_date' => now()->addDays(5)->format('Y-m-d'),
                ],
            ],
        ], $this->adminUser->id);

        $item = $movement->items->first();

        // Devolução parcial (1 unidade)
        $loanService->processReturn($item, 1);
        $this->assertEquals(2, $this->materialFerramenta->fresh()->current_stock);
        $this->assertEquals(MovementStatus::PARTIALLY_RETURNED, $movement->fresh()->status);

        // Devolução total (1 unidade restante)
        $loanService->processReturn($item->fresh(), 1);
        $this->assertEquals(3, $this->materialFerramenta->fresh()->current_stock);
        $this->assertEquals(MovementStatus::COMPLETED, $movement->fresh()->status);
        $this->assertEquals(ItemStatus::RETURNED, $item->fresh()->status);
    }

    public function test_user_with_consulta_role_cannot_create_movements(): void
    {
        $response = $this->actingAs($this->consultaUser)
            ->get(route('movements.create'));

        $response->assertStatus(403);
    }
}
