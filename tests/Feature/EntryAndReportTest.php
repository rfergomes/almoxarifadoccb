<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\MovementType;
use App\Models\Category;
use App\Models\Material;
use App\Models\User;
use App\Services\EntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntryAndReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Material $material;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();

        $category = Category::create(['name' => 'Materiais Gerais']);
        $this->material = Material::create([
            'code_sku' => 'MAT-ENT-001',
            'name' => 'Cimento CP II 50kg',
            'category_id' => $category->id,
            'unit_measure' => 'CX',
            'current_stock' => 10,
            'minimum_stock' => 5,
            'is_returnable' => false,
        ]);
    }

    public function test_entry_registration_increments_stock_and_creates_document(): void
    {
        $entryService = app(EntryService::class);

        $data = [
            'document_type' => DocumentType::NOTA_FISCAL->value,
            'document_number' => 'NF-998877',
            'supplier_or_donor' => 'Votorantim Cimentos S.A.',
            'total_amount' => 1500.00,
            'notes' => 'Entrada por compra de materiais de reforma',
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'quantity' => 50,
                ],
            ],
        ];

        $movement = $entryService->createEntry($data, $this->adminUser->id);

        $this->assertEquals(MovementType::ENTRY, $movement->type);
        $this->assertEquals('NF-998877', $movement->entryDocument->document_number);
        $this->assertEquals(60, $this->material->fresh()->current_stock);
    }

    public function test_quick_beneficiary_ajax_modal_returns_json_and_creates_record(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('api.quick-beneficiary'), [
                'name' => 'Irmão Lucas Santos',
                'document_cpf' => '123.456.789-00',
                'phone' => '(11) 99999-8888',
                'role_in_ccb' => 'Voluntário Obra',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Irmão Lucas Santos (Voluntário Obra)',
                ],
            ]);
    }

    public function test_quick_destination_ajax_modal_returns_json_and_creates_record(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('api.quick-destination'), [
                'code' => 'CO-999',
                'name' => 'C.O. Teste Modal',
                'type' => 'casa_de_oracao',
                'city' => 'São Paulo',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'CO-999 - C.O. Teste Modal',
                ],
            ]);
    }

    public function test_reports_index_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('reports.index'));

        $response->assertStatus(200);
    }

    public function test_reports_pdf_download_returns_ok(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('reports.export.pdf', ['type' => 'inventory']));

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }
}
