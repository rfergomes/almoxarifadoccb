<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialObservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();
        $this->category = Category::create(['name' => 'Materiais Hidráulicos']);
    }

    public function test_material_can_be_created_with_notes(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.store'), [
                'name' => 'Tubo PVC 100mm Esgoto 6m',
                'category_id' => $this->category->id,
                'unit_measure' => 'BR',
                'current_stock' => 15,
                'minimum_stock' => 5,
                'notes' => "Armazenar na horizontal em prateleira coberta.\nMarca homologada: Tigre ou Amanco.",
                'is_returnable' => false,
                'status' => true,
            ]);

        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('success');

        $material = Material::where('name', 'Tubo PVC 100mm Esgoto 6m')->first();
        $this->assertNotNull($material);
        $this->assertStringContainsString('Armazenar na horizontal', (string) $material->notes);
        $this->assertTrue($material->hasNotes());
    }

    public function test_material_creation_fails_when_notes_exceeds_maximum_characters(): void
    {
        $longNotes = str_repeat('A', 2001);

        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.store'), [
                'name' => 'Válvula de Retenção',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'current_stock' => 5,
                'minimum_stock' => 1,
                'notes' => $longNotes,
                'is_returnable' => false,
                'status' => true,
            ]);

        $response->assertSessionHasErrors('notes');
        $this->assertDatabaseMissing('materials', [
            'name' => 'Válvula de Retenção',
        ]);
    }

    public function test_material_can_be_updated_with_new_notes(): void
    {
        $material = Material::create([
            'code_sku' => 'CCB-901',
            'name' => 'Joelho 90 100mm',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 10,
            'minimum_stock' => 2,
            'notes' => 'Observação original de teste.',
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('materials.update', $material), [
                'code_sku' => 'CCB-901',
                'name' => 'Joelho 90 100mm',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'minimum_stock' => 2,
                'status' => true,
                'is_returnable' => false,
                'notes' => 'Nova observação técnica atualizada com sucesso.',
            ]);

        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('success');

        $material->refresh();
        $this->assertEquals('Nova observação técnica atualizada com sucesso.', $material->notes);
    }

    public function test_material_notes_can_be_cleared_on_update(): void
    {
        $material = Material::create([
            'code_sku' => 'CCB-902',
            'name' => 'Curva 45 100mm',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 10,
            'minimum_stock' => 2,
            'notes' => 'Observação anterior que será limpa.',
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('materials.update', $material), [
                'code_sku' => 'CCB-902',
                'name' => 'Curva 45 100mm',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'minimum_stock' => 2,
                'status' => true,
                'is_returnable' => false,
                'notes' => '',
            ]);

        $response->assertRedirect(route('materials.index'));
        $material->refresh();
        $this->assertNull($material->notes);
        $this->assertFalse($material->hasNotes());
    }

    public function test_quick_material_creation_saves_notes_via_api(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('api.quick-material'), [
                'name' => 'Fita Veda Rosca 18mmx50m',
                'category_id' => $this->category->id,
                'unit_measure' => 'RL',
                'current_stock' => 20,
                'minimum_stock' => 5,
                'notes' => 'Cadastro rápido via entrada com nota técnica.',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Fita Veda Rosca 18mmx50m',
                    'notes' => 'Cadastro rápido via entrada com nota técnica.',
                ],
            ]);

        $material = Material::where('name', 'Fita Veda Rosca 18mmx50m')->first();
        $this->assertNotNull($material);
        $this->assertEquals('Cadastro rápido via entrada com nota técnica.', $material->notes);
    }

    public function test_material_list_renders_observation_tooltip_for_items_with_notes(): void
    {
        $materialWithNotes = Material::create([
            'code_sku' => 'CCB-903',
            'name' => 'Fita Isolante 3M Imperial',
            'category_id' => $this->category->id,
            'unit_measure' => 'RL',
            'current_stock' => 10,
            'minimum_stock' => 2,
            'notes' => 'Classe A de isolamento até 750V.',
            'is_returnable' => false,
            'status' => true,
        ]);

        $materialWithoutNotes = Material::create([
            'code_sku' => 'CCB-904',
            'name' => 'Parafuso Philips 4x40',
            'category_id' => $this->category->id,
            'unit_measure' => 'CX',
            'current_stock' => 5,
            'minimum_stock' => 1,
            'notes' => null,
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('materials.index'));

        $response->assertOk();
        $response->assertSee('Classe A de isolamento até 750V.');
        $response->assertSee('bi-chat-left-text-fill');
        $response->assertSee('data-bs-toggle="tooltip"', false);
    }

    public function test_search_by_material_notes_finds_correct_material(): void
    {
        Material::create([
            'code_sku' => 'CCB-905',
            'name' => 'Disjuntor Bipolar 32A',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 8,
            'minimum_stock' => 2,
            'notes' => 'Curva C norma NBR NM 60898 Schneider.',
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('materials.index', ['search' => 'Schneider']));

        $response->assertOk();
        $response->assertSee('Disjuntor Bipolar 32A');
    }
}
