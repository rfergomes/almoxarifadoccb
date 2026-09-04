<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ItemStatus;
use App\Enums\MovementStatus;
use App\Enums\MovementType;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\EntryDocument;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovementNavigationAndScopeTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Movement $entryMovement;
    protected Movement $consumptionMovement;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();

        $category = Category::create(['name' => 'Materiais']);
        $material = Material::create([
            'code_sku' => 'NAV-001',
            'name' => 'Item Navegação',
            'category_id' => $category->id,
            'unit_measure' => 'UN',
            'current_stock' => 50,
            'minimum_stock' => 5,
            'is_returnable' => false,
        ]);

        $beneficiary = Beneficiary::create([
            'name' => 'Beneficiário Teste',
            'role_in_ccb' => 'Voluntário',
            'status' => true,
        ]);

        $destination = Destination::create([
            'code' => 'DST-001',
            'name' => 'Destino Teste',
            'type' => 'casa_de_oracao',
            'status' => true,
        ]);

        // Movimentação de Entrada
        $this->entryMovement = Movement::create([
            'code' => 'ENT-TEST-001',
            'type' => MovementType::ENTRY,
            'status' => MovementStatus::COMPLETED,
            'user_id' => $this->adminUser->id,
        ]);

        EntryDocument::create([
            'movement_id' => $this->entryMovement->id,
            'document_type' => 'NOTA_FISCAL',
            'document_number' => 'NF-12345',
            'supplier_or_donor' => 'Fornecedor Teste',
            'total_amount' => 150.00,
        ]);

        MovementItem::create([
            'movement_id' => $this->entryMovement->id,
            'material_id' => $material->id,
            'quantity' => 10,
            'status' => ItemStatus::DELIVERED,
        ]);

        // Movimentação de Saída
        $this->consumptionMovement = Movement::create([
            'code' => 'SAI-TEST-001',
            'type' => MovementType::CONSUMPTION,
            'status' => MovementStatus::COMPLETED,
            'user_id' => $this->adminUser->id,
            'beneficiary_id' => $beneficiary->id,
            'destination_id' => $destination->id,
        ]);

        MovementItem::create([
            'movement_id' => $this->consumptionMovement->id,
            'material_id' => $material->id,
            'quantity' => 5,
            'status' => ItemStatus::DELIVERED,
        ]);
    }

    public function test_movements_index_excludes_entries(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('movements.index'));

        $response->assertOk();
        $response->assertSee('SAI-TEST-001');
        $response->assertDontSee('ENT-TEST-001');
    }

    public function test_movement_show_for_entry_activates_entries_menu_and_returns_to_entries(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('movements.show', $this->entryMovement));

        $response->assertOk();
        $response->assertSee(route('entries.index'));
        $response->assertSee('ENT-TEST-001');
    }

    public function test_movement_show_for_consumption_activates_saidas_menu_and_returns_to_movements(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('movements.show', $this->consumptionMovement));

        $response->assertOk();
        $response->assertSee(route('movements.index'));
        $response->assertSee('SAI-TEST-001');
    }
}
