<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        Storage::fake('public');

        $this->user = User::where('email', 'admin@ccb.org.br')->first();
    }

    /**
     * Cria um UploadedFile simulando imagem válida (PNG) sem depender da extensão GD.
     */
    protected function createFakeImage(string $filename = 'avatar.png', int $sizeKb = 20): UploadedFile
    {
        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $content = base64_decode($pngBase64);
        if ($sizeKb > 1) {
            $content .= str_repeat("\0", ($sizeKb - 1) * 1024);
        }

        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->email);
        $response->assertSee('Meu Perfil');
        $response->assertSee('Informações Pessoais');
        $response->assertSee('Segurança');
    }

    public function test_user_can_update_profile_name(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Administrador Atualizado CCB',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Administrador Atualizado CCB', $this->user->name);
    }

    public function test_user_can_upload_valid_avatar(): void
    {
        $avatar = $this->createFakeImage('perfil_ccb.png', 50);

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => $this->user->name,
            'avatar' => $avatar,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertNotNull($this->user->avatar_path);
        Storage::disk('public')->assertExists($this->user->avatar_path);
        $this->assertTrue($this->user->hasAvatar());
        $this->assertStringContainsString('storage/avatars/', (string) $this->user->avatar_url);
    }

    public function test_user_cannot_upload_invalid_file_as_avatar(): void
    {
        $fakeTextFile = UploadedFile::fake()->create('documento.txt', 10, 'text/plain');

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => $this->user->name,
            'avatar' => $fakeTextFile,
        ]);

        $response->assertSessionHasErrors(['avatar']);
        $this->user->refresh();
        $this->assertNull($this->user->avatar_path);
    }

    public function test_user_cannot_upload_avatar_larger_than_5mb(): void
    {
        $largeAvatar = $this->createFakeImage('foto_gigante.png', 5200);

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => $this->user->name,
            'avatar' => $largeAvatar,
        ]);

        $response->assertSessionHasErrors(['avatar']);
        $this->user->refresh();
        $this->assertNull($this->user->avatar_path);
    }

    public function test_user_can_remove_avatar(): void
    {
        // Primeiro faz upload
        $avatar = $this->createFakeImage('foto_remover.png', 30);
        $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => $this->user->name,
            'avatar' => $avatar,
        ]);

        $this->user->refresh();
        $storedPath = $this->user->avatar_path;
        $this->assertNotNull($storedPath);
        Storage::disk('public')->assertExists($storedPath);

        // Agora solicita a remoção
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => $this->user->name,
            'remove_avatar' => '1',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertNull($this->user->avatar_path);
        $this->assertFalse($this->user->hasAvatar());
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.password.update'), [
            'current_password' => '12345678',
            'password' => 'NovaSenha@2026',
            'password_confirmation' => 'NovaSenha@2026',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('NovaSenha@2026', $this->user->password));
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.password.update'), [
            'current_password' => 'senha_errada',
            'password' => 'NovaSenha@2026',
            'password_confirmation' => 'NovaSenha@2026',
        ]);

        $response->assertSessionHasErrors(['current_password']);

        $this->user->refresh();
        $this->assertTrue(Hash::check('12345678', $this->user->password));
    }

    public function test_user_initials_generation(): void
    {
        $this->user->name = 'Daniel Oliveira';
        $this->assertEquals('DO', $this->user->initials());

        $this->user->name = 'Rodrigo Ferreira Lima';
        $this->assertEquals('RL', $this->user->initials());

        $this->user->name = 'Almoxarife';
        $this->assertEquals('AL', $this->user->initials());
    }

    public function test_navbar_displays_avatar_or_initials_for_authenticated_user(): void
    {
        // Sem avatar: renderiza iniciais
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee($this->user->initials());
        $response->assertSee(route('profile.edit'));

        // Com avatar: renderiza tag img do avatar
        $avatar = $this->createFakeImage('avatar_nav.png', 20);
        $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => $this->user->name,
            'avatar' => $avatar,
        ]);

        $this->user->refresh();
        $responseWithAvatar = $this->actingAs($this->user)->get(route('dashboard'));
        $responseWithAvatar->assertStatus(200);
        $responseWithAvatar->assertSee($this->user->avatar_url);
    }

    public function test_users_table_displays_user_avatar(): void
    {
        $response = $this->actingAs($this->user)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        // Exibe iniciais ou avatar
        $response->assertSee($this->user->initials());
    }
}
