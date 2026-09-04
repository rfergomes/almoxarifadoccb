<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\MovementType;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\EntryDocument;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolesAndSafeDeletionTest extends TestCase
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

    public function test_user_created_with_store_has_exactly_one_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Novo Almoxarife Teste',
            'email' => 'novo.almoxarife@ccb.org.br',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'role' => 'Almoxarife',
            'status' => 1,
        ]);

        $response->assertRedirect(route('users.index'));

        $user = User::where('email', 'novo.almoxarife@ccb.org.br')->first();
        $this->assertNotNull($user);
        $this->assertCount(1, $user->roles);
        $this->assertEquals('Almoxarife', $user->primary_role);
        $this->assertEquals('primary', $user->primary_role_badge);
    }

    public function test_sanitize_all_user_roles_resolves_multiple_roles_by_hierarchy(): void
    {
        // Cria usuário e atribui dois papéis simultaneamente (simulando estado corrompido)
        $user = User::create([
            'name' => 'Usuário Multi Papel',
            'email' => 'multi@ccb.org.br',
            'password' => 'secret123',
            'status' => true,
        ]);
        $user->assignRole('Consulta');
        $user->assignRole('Almoxarife');

        $this->assertCount(2, $user->fresh()->roles);
        $this->assertEquals('Almoxarife', $user->primary_role);

        // Executa sanitização
        $sanitized = User::sanitizeAllUserRoles();
        $this->assertEquals(1, $sanitized);

        $user->refresh();
        $this->assertCount(1, $user->roles);
        $this->assertTrue($user->hasRole('Almoxarife'));
        $this->assertFalse($user->hasRole('Consulta'));
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_cannot_delete_last_admin(): void
    {
        // Só há 1 admin no sistema
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_cannot_delete_user_with_movement_history(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-MOV-USR',
            'name' => 'Tinta Branca 18L',
            'category_id' => $this->category->id,
            'unit_measure' => 'LATA',
            'current_stock' => 10,
            'minimum_stock' => 2,
            'is_returnable' => false,
            'status' => true,
        ]);

        Movement::create([
            'code' => 'MOV-TEST-AUDIT',
            'user_id' => $this->almoxarife->id,
            'beneficiary_id' => $this->beneficiary->id,
            'destination_id' => $this->destination->id,
            'type' => MovementType::CONSUMPTION,
            'status' => \App\Enums\MovementStatus::COMPLETED,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->almoxarife));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->almoxarife->id]);
    }

    public function test_admin_can_delete_user_without_movements(): void
    {
        $userToDelete = User::create([
            'name' => 'Usuário Sem Movimentação',
            'email' => 'sem.mov@ccb.org.br',
            'password' => 'secret123',
            'status' => true,
        ]);
        $userToDelete->syncRoles(['Consulta']);

        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $userToDelete));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_non_admin_cannot_delete_user(): void
    {
        $targetUser = User::create([
            'name' => 'Alvo',
            'email' => 'alvo@ccb.org.br',
            'password' => 'secret123',
            'status' => true,
        ]);

        $response = $this->actingAs($this->almoxarife)->delete(route('users.destroy', $targetUser));
        $response->assertStatus(403);
    }

    public function test_admin_cannot_delete_material_with_current_stock(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-STOCK-1',
            'name' => 'Cimento CP-II 50kg',
            'category_id' => $this->category->id,
            'unit_measure' => 'SC',
            'current_stock' => 5,
            'minimum_stock' => 1,
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('materials.destroy', $material));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('materials', ['id' => $material->id]);
    }

    public function test_admin_cannot_delete_material_with_movement_history(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-STOCK-0-HIST',
            'name' => 'Fita Crepe',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 0,
            'minimum_stock' => 1,
            'is_returnable' => false,
            'status' => true,
        ]);

        $mov = Movement::create([
            'code' => 'MOV-HIST-01',
            'user_id' => $this->admin->id,
            'beneficiary_id' => $this->beneficiary->id,
            'destination_id' => $this->destination->id,
            'type' => MovementType::CONSUMPTION,
            'status' => \App\Enums\MovementStatus::COMPLETED,
        ]);

        MovementItem::create([
            'movement_id' => $mov->id,
            'material_id' => $material->id,
            'quantity' => 2,
            'returned_quantity' => 0,
            'status' => \App\Enums\ItemStatus::DELIVERED,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('materials.destroy', $material));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('materials', ['id' => $material->id]);
    }

    public function test_admin_can_delete_unused_zero_stock_material(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-UNUSED',
            'name' => 'Parafuso Inox 6mm',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 0,
            'minimum_stock' => 0,
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('materials.destroy', $material));

        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
    }

    public function test_non_admin_cannot_delete_material(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-TEST-FORBIDDEN',
            'name' => 'Material Proibido',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 0,
            'minimum_stock' => 0,
            'is_returnable' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($this->almoxarife)->delete(route('materials.destroy', $material));
        $response->assertStatus(403);
    }

    public function test_admin_can_delete_consumption_movement_and_revert_stock(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-REV-CONS',
            'name' => 'Lâmpada LED 12W',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 7,
            'minimum_stock' => 2,
            'is_returnable' => false,
            'status' => true,
        ]);

        $mov = Movement::create([
            'code' => 'MOV-CONS-DEL',
            'user_id' => $this->admin->id,
            'beneficiary_id' => $this->beneficiary->id,
            'destination_id' => $this->destination->id,
            'type' => MovementType::CONSUMPTION,
            'status' => \App\Enums\MovementStatus::COMPLETED,
        ]);

        MovementItem::create([
            'movement_id' => $mov->id,
            'material_id' => $material->id,
            'quantity' => 3,
            'returned_quantity' => 0,
            'status' => \App\Enums\ItemStatus::DELIVERED,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('movements.destroy', $mov));

        $response->assertRedirect(route('movements.index'));
        $response->assertSessionHas('success');

        // Saldo deve ter subido de 7 para 10
        $this->assertEquals(10, $material->fresh()->current_stock);
        $this->assertDatabaseMissing('movements', ['id' => $mov->id]);
        $this->assertDatabaseMissing('movement_items', ['movement_id' => $mov->id]);
    }

    public function test_admin_cannot_delete_entry_movement_if_stock_would_be_negative(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-REV-ENT-NEG',
            'name' => 'Cabo Flexível 2.5mm',
            'category_id' => $this->category->id,
            'unit_measure' => 'M',
            'current_stock' => 2, // Entrada foi de 10, mas 8 já foram consumidos
            'minimum_stock' => 5,
            'is_returnable' => false,
            'status' => true,
        ]);

        $entryDoc = EntryDocument::create([
            'document_number' => 'NF-9999',
            'document_type' => \App\Enums\DocumentType::NOTA_FISCAL,
            'supplier_or_donor' => 'Fornecedor Elétrica',
        ]);

        $mov = Movement::create([
            'code' => 'ENT-NEG-TEST',
            'user_id' => $this->admin->id,
            'entry_document_id' => $entryDoc->id,
            'type' => MovementType::ENTRY,
            'status' => \App\Enums\MovementStatus::COMPLETED,
        ]);

        MovementItem::create([
            'movement_id' => $mov->id,
            'material_id' => $material->id,
            'quantity' => 10,
            'returned_quantity' => 0,
            'status' => \App\Enums\ItemStatus::DELIVERED,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('movements.destroy', $mov));

        $response->assertSessionHas('error');
        $this->assertEquals(2, $material->fresh()->current_stock);
        $this->assertDatabaseHas('movements', ['id' => $mov->id]);
    }

    public function test_admin_can_delete_entry_movement_and_decrement_stock(): void
    {
        $material = Material::create([
            'code_sku' => 'MAT-REV-ENT-OK',
            'name' => 'Disjuntor 20A',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 15,
            'minimum_stock' => 5,
            'is_returnable' => false,
            'status' => true,
        ]);

        $entryDoc = EntryDocument::create([
            'document_number' => 'NF-8888',
            'document_type' => \App\Enums\DocumentType::NOTA_FISCAL,
            'supplier_or_donor' => 'Fornecedor Elétrica',
        ]);

        $mov = Movement::create([
            'code' => 'ENT-OK-TEST',
            'user_id' => $this->admin->id,
            'entry_document_id' => $entryDoc->id,
            'type' => MovementType::ENTRY,
            'status' => \App\Enums\MovementStatus::COMPLETED,
        ]);

        MovementItem::create([
            'movement_id' => $mov->id,
            'material_id' => $material->id,
            'quantity' => 5,
            'returned_quantity' => 0,
            'status' => \App\Enums\ItemStatus::DELIVERED,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('movements.destroy', $mov));

        $response->assertRedirect(route('entries.index'));
        $response->assertSessionHas('success');

        // Estoque deve ser decrementado de 15 para 10
        $this->assertEquals(10, $material->fresh()->current_stock);
        $this->assertDatabaseMissing('movements', ['id' => $mov->id]);
        $this->assertDatabaseMissing('entry_documents', ['id' => $entryDoc->id]);
    }
}
