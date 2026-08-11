<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditAndInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Material $material;
    protected Beneficiary $beneficiary;
    protected Destination $destination;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();

        $category = Category::create(['name' => 'Ferramentas']);
        $this->material = Material::create([
            'code_sku' => 'EQP-100',
            'name' => 'Serra Tico-Tico',
            'category_id' => $category->id,
            'unit_measure' => 'UN',
            'current_stock' => 10,
            'minimum_stock' => 2,
            'is_returnable' => true,
            'status' => true,
        ]);

        $this->beneficiary = Beneficiary::create([
            'name' => 'Irmão Mário',
            'document_cpf' => '111.111.111-11',
            'status' => true,
        ]);

        $this->destination = Destination::create([
            'code' => 'CO-100',
            'name' => 'C.O. Central Teste',
            'type' => 'casa_de_oracao',
            'status' => true,
        ]);
    }

    public function test_material_update_does_not_change_current_stock(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('materials.update', $this->material), [
                'code_sku' => 'EQP-100',
                'name' => 'Serra Tico-Tico Bosch',
                'category_id' => $this->material->category_id,
                'unit_measure' => 'UN',
                'is_returnable' => 1,
                'minimum_stock' => 3,
                'status' => 1,
                'current_stock' => 999, // Tenta alterar estoque via form de edição
            ]);

        $response->assertRedirect(route('materials.index'));
        $this->assertEquals('Serra Tico-Tico Bosch', $this->material->fresh()->name);
        $this->assertEquals(10, $this->material->fresh()->current_stock); // Estoque permaneceu 10!
    }

    public function test_material_stock_adjustment_updates_stock_with_justification(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.adjust-stock', $this->material), [
                'new_stock' => 15,
                'justification' => 'Ajuste de inventário físico anual realizado.',
            ]);

        $response->assertRedirect(route('materials.index'));
        $this->assertEquals(15, $this->material->fresh()->current_stock);
    }

    public function test_beneficiary_update_modal_action(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('beneficiaries.update', $this->beneficiary), [
                'name' => 'Irmão Mário Silva',
                'document_cpf' => '111.111.111-11',
                'phone' => '(11) 98888-8888',
                'role_in_ccb' => 'Oficial de Manutenção',
                'status' => 1,
            ]);

        $response->assertRedirect(route('beneficiaries.index'));
        $this->assertEquals('Irmão Mário Silva', $this->beneficiary->fresh()->name);
    }

    public function test_destination_update_modal_action(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('destinations.update', $this->destination), [
                'code' => 'CO-100',
                'name' => 'C.O. Central Atualizada',
                'type' => 'casa_de_oracao',
                'city' => 'São Paulo',
                'status' => 1,
            ]);

        $response->assertRedirect(route('destinations.index'));
        $this->assertEquals('C.O. Central Atualizada', $this->destination->fresh()->name);
    }
}
