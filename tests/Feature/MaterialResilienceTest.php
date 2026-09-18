<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MaterialResilienceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();
        $this->category = Category::create(['name' => 'Materiais Elétricos']);
    }

    public function test_material_store_succeeds_normally(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->from(route('materials.index'))
            ->post(route('materials.store'), [
                'name' => 'Disjuntor Bipolar 32A',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'current_stock' => 20,
                'minimum_stock' => 5,
                'notes' => 'Padrão DIN curva C.',
                'is_returnable' => false,
                'status' => true,
            ]);

        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('success', 'Material cadastrado com sucesso!');

        $this->assertDatabaseHas('materials', [
            'name' => 'Disjuntor Bipolar 32A',
            'notes' => 'Padrão DIN curva C.',
        ]);
    }

    public function test_material_update_succeeds_normally(): void
    {
        $material = Material::create([
            'code_sku' => 'CCB-901',
            'name' => 'Cabo Flexível 2.5mm',
            'category_id' => $this->category->id,
            'unit_measure' => 'M',
            'current_stock' => 100,
            'minimum_stock' => 20,
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->from(route('materials.index'))
            ->put(route('materials.update', $material), [
                'code_sku' => 'CCB-901',
                'name' => 'Cabo Flexível 2.5mm Azul',
                'category_id' => $this->category->id,
                'unit_measure' => 'M',
                'minimum_stock' => 25,
                'notes' => 'Rolo fechado de 100 metros.',
                'is_returnable' => false,
                'status' => true,
            ]);

        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'name' => 'Cabo Flexível 2.5mm Azul',
            'notes' => 'Rolo fechado de 100 metros.',
        ]);
    }

    public function test_material_store_gracefully_handles_unexpected_exception(): void
    {
        // Simula falha na criação interceptando o evento creating do model
        Material::creating(function (): void {
            throw new \RuntimeException('Erro simulado de conexão com banco de dados');
        });

        $response = $this->actingAs($this->adminUser)
            ->from(route('materials.index'))
            ->post(route('materials.store'), [
                'name' => 'Lâmpada LED Tubular',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'current_stock' => 10,
                'minimum_stock' => 2,
                'notes' => 'Bivolt 18W.',
                'is_returnable' => false,
                'status' => true,
            ]);

        // Não deve estourar tela 500, deve redirecionar de volta com inputs e flash de erro
        $response->assertStatus(302);
        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('error');
        $response->assertSessionHasInput('name', 'Lâmpada LED Tubular');
        $response->assertSessionHasInput('notes', 'Bivolt 18W.');
    }

    public function test_material_update_gracefully_handles_unexpected_exception(): void
    {
        $material = Material::create([
            'code_sku' => 'CCB-902',
            'name' => 'Tomada 2P+T 10A',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 50,
            'minimum_stock' => 10,
            'is_returnable' => false,
            'status' => true,
        ]);

        Material::updating(function (): void {
            throw new \RuntimeException('Erro simulado de atualização');
        });

        $response = $this->actingAs($this->adminUser)
            ->from(route('materials.index'))
            ->put(route('materials.update', $material), [
                'code_sku' => 'CCB-902',
                'name' => 'Tomada 2P+T 20A',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'minimum_stock' => 10,
                'notes' => 'Modelo modular.',
                'is_returnable' => false,
                'status' => true,
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('error');
        $response->assertSessionHasInput('name', 'Tomada 2P+T 20A');
    }

    public function test_material_search_filters_normally_without_server_error(): void
    {
        Material::create([
            'code_sku' => 'CCB-903',
            'name' => 'Material Para Teste Especial',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 15,
            'minimum_stock' => 5,
            'is_returnable' => false,
            'status' => true,
        ]);

        // Simula busca pelo termo "teste" (como informado pelo usuário)
        $response = $this->actingAs($this->adminUser)
            ->get(route('materials.index', ['search' => 'teste']));

        $response->assertOk();
        $response->assertSee('Material Para Teste Especial');
        $response->assertSee('CCB-903');
    }
}
