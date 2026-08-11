<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();
    }

    public function test_forgot_password_page_loads(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200)
            ->assertSee('Recuperar Senha');
    }

    public function test_can_request_password_reset_link(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'admin@ccb.org.br',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'admin@ccb.org.br',
        ]);
    }

    public function test_cannot_request_reset_for_nonexisting_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'naoexistente@ccb.org.br',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_can_reset_password_with_valid_token(): void
    {
        $token = 'test-plain-token-123';
        DB::table('password_reset_tokens')->insert([
            'email' => 'admin@ccb.org.br',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'admin@ccb.org.br',
            'password' => 'NovaSenhaSegura123',
            'password_confirmation' => 'NovaSenhaSegura123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NovaSenhaSegura123', $this->adminUser->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'admin@ccb.org.br']);
    }
}
