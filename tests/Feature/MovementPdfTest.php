<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Material;
use App\Models\Movement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovementPdfTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Movement $movement;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();

        $category = Category::create(['name' => 'Elétrica']);
        $material = Material::create([
            'code_sku' => 'ELE-005',
            'name' => 'Fita Isolante 3M 20m',
            'category_id' => $category->id,
            'unit_measure' => 'UN',
            'current_stock' => 50,
            'minimum_stock' => 5,
            'status' => true,
        ]);

        $beneficiary = Beneficiary::create(['name' => 'Irmão Mário', 'status' => true]);
        $destination = Destination::create(['code' => 'CO-300', 'name' => 'C.O. Teste', 'type' => 'casa_de_oracao', 'status' => true]);

        $stockService = app(StockService::class);
        $this->movement = $stockService->createMovement([
            'type' => 'CONSUMPTION',
            'beneficiary_id' => $beneficiary->id,
            'destination_id' => $destination->id,
            'items' => [
                ['material_id' => $material->id, 'quantity' => 5],
            ],
        ], $this->adminUser->id);
    }

    public function test_movement_pdf_download_returns_ok(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('movements.pdf', $this->movement));

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }
}
