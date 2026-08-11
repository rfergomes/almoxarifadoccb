<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovementTypeRulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Material $consumableMaterial;
    protected Material $returnableMaterial;
    protected Material $epiMaterial;
    protected Beneficiary $beneficiary;
    protected Destination $destination;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();

        $catGeneral = Category::create(['name' => 'Materiais Gerais']);
        $catEpi = Category::create(['name' => 'EPI']);

        $this->consumableMaterial = Material::create([
            'code_sku' => 'MAT-CON-001',
            'name' => 'Cimento CP II 50kg',
            'category_id' => $catGeneral->id,
            'unit_measure' => 'CX',
            'current_stock' => 50,
            'minimum_stock' => 10,
            'is_returnable' => false, // Consumo
            'status' => true,
        ]);

        $this->returnableMaterial = Material::create([
            'code_sku' => 'EQP-001',
            'name' => 'Furadeira de Impacto Industrial',
            'category_id' => $catGeneral->id,
            'unit_measure' => 'UN',
            'current_stock' => 5,
            'minimum_stock' => 1,
            'is_returnable' => true, // Retornável
            'status' => true,
        ]);

        $this->epiMaterial = Material::create([
            'code_sku' => 'EPI-001',
            'name' => 'Capacete de Segurança com Jugular',
            'category_id' => $catEpi->id,
            'unit_measure' => 'UN',
            'current_stock' => 20,
            'minimum_stock' => 5,
            'ca_number' => '12345',
            'ca_validity' => date('Y-m-d', strtotime('+1 year')),
            'is_returnable' => true,
            'status' => true,
        ]);

        $this->beneficiary = Beneficiary::create([
            'name' => 'Irmão João Silva',
            'status' => true,
        ]);

        $this->destination = Destination::create([
            'code' => 'CO-200',
            'name' => 'C.O. Teste Nova Odessa',
            'type' => 'casa_de_oracao',
            'status' => true,
        ]);
    }

    public function test_cannot_loan_consumable_material(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('movements.store'), [
                'type' => 'LOAN',
                'beneficiary_id' => $this->beneficiary->id,
                'destination_id' => $this->destination->id,
                'items' => [
                    [
                        'material_id' => $this->consumableMaterial->id, // Tenta emprestar Cimento (consumo)
                        'quantity' => 2,
                        'expected_return_date' => date('Y-m-d', strtotime('+7 days')),
                    ],
                ],
            ]);

        $response->assertSessionHasErrors(['items.0.material_id']);
    }

    public function test_cannot_consume_returnable_equipment(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('movements.store'), [
                'type' => 'CONSUMPTION',
                'beneficiary_id' => $this->beneficiary->id,
                'destination_id' => $this->destination->id,
                'items' => [
                    [
                        'material_id' => $this->returnableMaterial->id, // Tenta dar baixa definitiva em Furadeira (retornável) em consumo
                        'quantity' => 1,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors(['items.0.material_id']);
    }

    public function test_cannot_deliver_non_epi_material_as_epi(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('movements.store'), [
                'type' => 'EPI',
                'beneficiary_id' => $this->beneficiary->id,
                'destination_id' => $this->destination->id,
                'items' => [
                    [
                        'material_id' => $this->returnableMaterial->id, // Tenta entregar Furadeira como se fosse EPI
                        'quantity' => 1,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors(['items.0.material_id']);
    }

    public function test_can_deliver_valid_epi(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('movements.store'), [
                'type' => 'EPI',
                'beneficiary_id' => $this->beneficiary->id,
                'destination_id' => $this->destination->id,
                'items' => [
                    [
                        'material_id' => $this->epiMaterial->id, // Entrega capacete EPI
                        'quantity' => 1,
                    ],
                ],
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(19, $this->epiMaterial->fresh()->current_stock);
    }
}
