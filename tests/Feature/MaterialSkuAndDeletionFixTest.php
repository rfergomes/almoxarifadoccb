<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ItemStatus;
use App\Enums\MovementStatus;
use App\Enums\MovementType;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialSkuAndDeletionFixTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $almoxarife;
    protected Category $category;
    protected Beneficiary $beneficiary;
    protected Destination $destination;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(CategorySeeder::class);

        $this->admin = User::where('email', 'admin@ccb.org.br')->first();
        $this->almoxarife = User::where('email', 'almoxarife@ccb.org.br')->first();
        $this->category = Category::first();

        $this->beneficiary = Beneficiary::create([
            'name' => 'Irmão Lucas Santos',
            'role_in_ccb' => 'Voluntário da Manutenção',
            'phone' => '19999999999',
            'status' => true,
        ]);

        $this->destination = Destination::create([
            'name' => 'Casa de Oração Central',
            'code' => 'CCB-001',
            'type' => 'casa_de_oracao',
            'status' => true,
        ]);
    }

    /**
     * US4: Layout deve carregar toastr.min.js e não carregar toastr.min.css em tag script.
     */
    public function test_layout_includes_toastr_javascript_correctly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('materials.index'));

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('toastr.js/latest/toastr.min.js', $content);
        $this->assertStringNotContainsString('<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"></script>', $content);
    }

    /**
     * US1: Administrador pode excluir com sucesso material elegível (estoque zero e sem movimentações).
     */
    public function test_admin_can_safely_delete_eligible_material(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-DEL-01',
            'name' => 'Material Sem Uso Para Exclusao',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 0,
            'minimum_stock' => 5,
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('materials.destroy', $material));

        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('success', "Material 'Material Sem Uso Para Exclusao' excluído com sucesso!");
        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
    }

    /**
     * US2: Bloqueio de exclusão caso o material possua saldo em estoque.
     */
    public function test_blocks_deletion_of_material_with_positive_stock(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-STOCK-01',
            'name' => 'Material Com Estoque',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 10,
            'minimum_stock' => 5,
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('materials.destroy', $material));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('materials', ['id' => $material->id]);
    }

    /**
     * US2: Bloqueio de exclusão caso o material possua histórico de movimentações.
     */
    public function test_blocks_deletion_of_material_with_movement_history(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-MOV-01',
            'name' => 'Material Com Historico',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 0,
            'minimum_stock' => 5,
            'is_returnable' => false,
            'status' => true,
        ]);

        $mov = Movement::create([
            'code' => 'MOV-TEST-001',
            'user_id' => $this->admin->id,
            'beneficiary_id' => $this->beneficiary->id,
            'destination_id' => $this->destination->id,
            'type' => MovementType::CONSUMPTION,
            'status' => MovementStatus::COMPLETED,
        ]);

        MovementItem::create([
            'movement_id' => $mov->id,
            'material_id' => $material->id,
            'quantity' => 2,
            'returned_quantity' => 0,
            'status' => ItemStatus::DELIVERED,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('materials.destroy', $material));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('materials', ['id' => $material->id]);
    }

    /**
     * US3: Criar material sem informar SKU gera automaticamente o sequencial GEN-001.
     */
    public function test_creates_material_with_automatic_sequential_sku_when_left_blank(): void
    {
        $response = $this->actingAs($this->admin)->post(route('materials.store'), [
            'name' => 'Item Sem SKU Informado',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 5,
            'minimum_stock' => 2,
            'is_returnable' => false,
            'status' => true,
        ]);

        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('success');

        $material = Material::where('name', 'Item Sem SKU Informado')->first();
        $this->assertNotNull($material);
        $this->assertSame('GEN-001', $material->code_sku);
    }

    /**
     * US3: Incrementa sequencialmente o SKU baseado no maior número existente.
     */
    public function test_increments_sequential_sku_based_on_highest_existing_gen_code(): void
    {
        Material::create([
            'code_sku' => 'GEN-001',
            'name' => 'Primeiro Generico',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 1,
            'minimum_stock' => 1,
            'is_returnable' => false,
        ]);

        Material::create([
            'code_sku' => 'GEN-009',
            'name' => 'Nono Generico',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 1,
            'minimum_stock' => 1,
            'is_returnable' => false,
        ]);

        // Próximo deve ser GEN-010
        $response = $this->actingAs($this->admin)->post(route('materials.store'), [
            'code_sku' => '',
            'name' => 'Decimo Generico',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 1,
            'minimum_stock' => 1,
            'is_returnable' => false,
        ]);

        $response->assertRedirect(route('materials.index'));

        $material = Material::where('name', 'Decimo Generico')->first();
        $this->assertNotNull($material);
        $this->assertSame('GEN-010', $material->code_sku);
    }

    /**
     * US3: Preserva código customizado informado pelo usuário.
     */
    public function test_preserves_custom_sku_when_provided(): void
    {
        $response = $this->actingAs($this->admin)->post(route('materials.store'), [
            'code_sku' => 'TINTA-CORAL-18L',
            'name' => 'Tinta Coral 18L',
            'category_id' => $this->category->id,
            'unit_measure' => 'LT',
            'current_stock' => 4,
            'minimum_stock' => 2,
            'is_returnable' => false,
        ]);

        $response->assertRedirect(route('materials.index'));

        $material = Material::where('name', 'Tinta Coral 18L')->first();
        $this->assertNotNull($material);
        $this->assertSame('TINTA-CORAL-18L', $material->code_sku);
    }

    /**
     * US3: Cadastro rápido de material via AJAX também gera sequencial quando vazio.
     */
    public function test_quick_registration_generates_sequential_sku(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('api.quick-material'), [
            'name' => 'Broca 10mm Rapida',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 0,
            'minimum_stock' => 1,
            'is_returnable' => true,
        ]);

        $response->assertCreated();
        $response->assertJson([
            'success' => true,
            'data' => [
                'code_sku' => 'GEN-001',
                'name' => 'Broca 10mm Rapida',
            ],
        ]);
    }
}
