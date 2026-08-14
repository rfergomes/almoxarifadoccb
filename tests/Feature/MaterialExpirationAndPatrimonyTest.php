<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ExpirationStatus;
use App\Models\Category;
use App\Models\Material;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialExpirationAndPatrimonyTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();
        $this->category = Category::create(['name' => 'Tintas e Massas']);
    }

    public function test_material_can_be_created_with_expiration_date_and_patrimony_code(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.store'), [
                'code_sku' => 'TNT-001',
                'name' => 'Tinta Acrílica Branca 18L',
                'category_id' => $this->category->id,
                'unit_measure' => 'GL',
                'current_stock' => 10,
                'minimum_stock' => 2,
                'expiration_date' => now()->addDays(20)->format('Y-m-d'),
                'patrimony_code' => 'PAT-TNT-001',
                'is_returnable' => 0,
                'status' => 1,
            ]);

        $response->assertRedirect(route('materials.index'));
        $this->assertDatabaseHas('materials', [
            'code_sku' => 'TNT-001',
            'patrimony_code' => 'PAT-TNT-001',
        ]);

        $material = Material::where('code_sku', 'TNT-001')->first();
        $this->assertNotNull($material->expiration_date);
        $this->assertTrue($material->isExpiringSoon(30));
        $this->assertFalse($material->isExpired());
    }

    public function test_material_expiration_status_calculation(): void
    {
        $expired = Material::create([
            'code_sku' => 'EXP-001',
            'name' => 'Massa Corrida Vencida',
            'category_id' => $this->category->id,
            'unit_measure' => 'CX',
            'current_stock' => 5,
            'minimum_stock' => 1,
            'expiration_date' => now()->subDays(5),
            'status' => true,
        ]);

        $expiringSoon = Material::create([
            'code_sku' => 'EXP-002',
            'name' => 'Grafiato a Vencer',
            'category_id' => $this->category->id,
            'unit_measure' => 'GL',
            'current_stock' => 8,
            'minimum_stock' => 2,
            'expiration_date' => now()->addDays(10),
            'status' => true,
        ]);

        $valid = Material::create([
            'code_sku' => 'EXP-003',
            'name' => 'Tinta Validade Longa',
            'category_id' => $this->category->id,
            'unit_measure' => 'GL',
            'current_stock' => 15,
            'minimum_stock' => 3,
            'expiration_date' => now()->addMonths(6),
            'status' => true,
        ]);

        $this->assertEquals(ExpirationStatus::EXPIRED, $expired->expirationStatus());
        $this->assertTrue($expired->isExpired());

        $this->assertEquals(ExpirationStatus::EXPIRING_SOON, $expiringSoon->expirationStatus());
        $this->assertTrue($expiringSoon->isExpiringSoon(30));

        $this->assertEquals(ExpirationStatus::VALID, $valid->expirationStatus());
        $this->assertFalse($valid->isExpired());
    }

    public function test_material_scopes_for_expired_and_expiring_soon(): void
    {
        Material::create([
            'code_sku' => 'MAT-EXPIRED',
            'name' => 'Produto 1 Vencido',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'expiration_date' => now()->subDays(2),
            'status' => true,
        ]);

        Material::create([
            'code_sku' => 'MAT-SOON',
            'name' => 'Produto 2 A Vencer',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'expiration_date' => now()->addDays(15),
            'status' => true,
        ]);

        $this->assertEquals(1, Material::expired()->count());
        $this->assertEquals(1, Material::expiringSoon(30)->count());
    }

    public function test_material_list_filtering_by_expiration_status_and_patrimony(): void
    {
        Material::create([
            'code_sku' => 'FILTER-1',
            'name' => 'Ferramenta Patrimoniada',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'patrimony_code' => 'PAT-FILTER-1',
            'status' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('materials.index', ['has_patrimony' => '1']));

        $response->assertOk();
        $response->assertSee('PAT-FILTER-1');
    }

    public function test_patrimony_code_uniqueness_validation(): void
    {
        Material::create([
            'code_sku' => 'PAT-1',
            'name' => 'Item Patrimônio 1',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'patrimony_code' => 'DUP-123',
            'status' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.store'), [
                'code_sku' => 'PAT-2',
                'name' => 'Item Patrimônio 2',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'current_stock' => 1,
                'minimum_stock' => 0,
                'patrimony_code' => 'DUP-123', // Código duplicado!
                'status' => 1,
            ]);

        $response->assertSessionHasErrors('patrimony_code');
    }

    public function test_expiration_report_generation(): void
    {
        Material::create([
            'code_sku' => 'REP-1',
            'name' => 'Resina Vencida',
            'category_id' => $this->category->id,
            'unit_measure' => 'GL',
            'expiration_date' => now()->subDays(10),
            'status' => true,
        ]);

        /** @var ReportService $reportService */
        $reportService = app(ReportService::class);
        $report = $reportService->getExpirationReport('expired');

        $this->assertCount(1, $report);
        $this->assertEquals('REP-1', $report->first()->code_sku);
    }
}
