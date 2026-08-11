<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
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

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('users.index'));

        $response->assertStatus(200)
            ->assertSee('Gestão de Usuários')
            ->assertSee('admin@ccb.org.br');
    }

    public function test_non_admin_cannot_view_users_list(): void
    {
        $response = $this->actingAs($this->almoxarifeUser)
            ->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('users.store'), [
                'name' => 'Novo Usuário Teste',
                'email' => 'novo.usuario@ccb.org.br',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'Almoxarife',
                'status' => 1,
            ]);

        $response->assertRedirect(route('users.index'));

        $newUser = User::where('email', 'novo.usuario@ccb.org.br')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->hasRole('Almoxarife'));
    }

    public function test_admin_can_update_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('users.update', $this->almoxarifeUser), [
                'name' => 'Almoxarife Principal Alterado',
                'email' => 'almoxarife@ccb.org.br',
                'role' => 'Consulta',
                'status' => 1,
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertEquals('Almoxarife Principal Alterado', $this->almoxarifeUser->fresh()->name);
        $this->assertTrue($this->almoxarifeUser->fresh()->hasRole('Consulta'));
    }

    public function test_admin_can_reset_user_password(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('users.reset-password', $this->almoxarifeUser), [
                'password' => 'novaSenha123',
                'password_confirmation' => 'novaSenha123',
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertTrue(Hash::check('novaSenha123', $this->almoxarifeUser->fresh()->password));
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->almoxarifeUser->update(['status' => false]);

        $response = $this->post('/login', [
            'email' => 'almoxarife@ccb.org.br',
            'password' => '12345678',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
}
