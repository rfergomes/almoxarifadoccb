<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $almoxarifeUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();
        $this->almoxarifeUser = User::where('email', 'almoxarife@ccb.org.br')->first();
    }

    public function test_admin_can_view_settings_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('settings.index'));

        $response->assertStatus(200)
            ->assertSee('Configurações do Sistema');
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $response = $this->actingAs($this->almoxarifeUser)
            ->get(route('settings.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('settings.update'), [
                'institution_name' => 'CONGREGAÇÃO CRISTÃ NO BRASIL - NOVA ODESSA',
                'administration_name' => 'Almoxarifado Central e Manutenção',
                'receipt_header_title' => 'Comprovante Oficial de Retirada/Entrada',
                'reports_header_title' => 'Relatório Oficial de Estoque',
                'inventory_header_title' => 'Folha de Inventário e Balanço',
                'support_email' => 'suporte.almoxarifado@ccb.org.br',
            ]);

        $response->assertRedirect(route('settings.index'));
        $this->assertEquals('CONGREGAÇÃO CRISTÃ NO BRASIL - NOVA ODESSA', Setting::get('institution_name'));
        $this->assertEquals('suporte.almoxarifado@ccb.org.br', Setting::get('support_email'));
    }
}
