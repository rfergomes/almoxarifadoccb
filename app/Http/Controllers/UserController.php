<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\NewUserCredentialsMail;
use App\Models\User;
use App\Services\UserAvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected UserAvatarService $userAvatarService
    ) {}

    public function index(): View
    {
        $users = User::with('roles')->latest()->paginate(15);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'status' => ['required', 'boolean'],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ], [
            'name.required' => 'Informe o nome completo do usuário.',
            'email.required' => 'Informe o e-mail corporativo.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'password.required' => 'Informe uma senha inicial para o usuário.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'role.required' => 'Selecione o perfil de acesso.',
            'avatar.image' => 'O arquivo de foto deve ser uma imagem válida.',
            'avatar.mimes' => 'A foto deve estar em formato JPG, PNG, WEBP ou GIF.',
            'avatar.max' => 'A foto não pode ultrapassar 5MB.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $this->userAvatarService->uploadAvatar($request->file('avatar'));
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'avatar_path' => $avatarPath,
            'password' => Hash::make($data['password']),
            'status' => $data['status'],
        ]);

        $user->syncRoles([$data['role']]);

        // Envio do e-mail de credenciais ao novo usuário
        try {
            Mail::to($user->email)->send(new NewUserCredentialsMail($user, $data['password']));
            $emailStatus = 'E-mail com credenciais enviado com sucesso!';
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de credenciais: ' . $e->getMessage());
            $emailStatus = 'Usuário cadastrado. (E-mail não pode ser entregue no servidor SMTP local).';
        }

        return redirect()->route('users.index')->with('success', "Usuário '{$user->name}' cadastrado com sucesso! {$emailStatus}");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'exists:roles,name'],
            'status' => ['required', 'boolean'],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
        ], [
            'avatar.image' => 'O arquivo de foto deve ser uma imagem válida.',
            'avatar.mimes' => 'A foto deve estar em formato JPG, PNG, WEBP ou GIF.',
            'avatar.max' => 'A foto não pode ultrapassar 5MB.',
        ]);

        if ($request->user()->id === $user->id && ! $data['status']) {
            return back()->with('error', 'Você não pode inativar a sua própria conta de usuário.');
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => $data['status'],
        ];

        if (!empty($request->input('remove_avatar'))) {
            $this->userAvatarService->deleteAvatar($user->avatar_path);
            $updateData['avatar_path'] = null;
        } elseif ($request->hasFile('avatar')) {
            $updateData['avatar_path'] = $this->userAvatarService->replaceAvatar(
                $request->file('avatar'),
                $user->avatar_path
            );
        }

        $user->update($updateData);

        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', "Cadastro do usuário '{$user->name}' atualizado com sucesso!");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ], [
            'password.required' => 'Informe a nova senha.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('users.index')->with('success', "Senha do usuário '{$user->name}' redefinida com sucesso!");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // 1. Não é permitido excluir a própria conta conectada
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Não é possível excluir a sua própria conta de usuário conectada.');
        }

        // 2. Não é permitido excluir o último administrador do sistema
        if ($user->hasRole('Administrador') && User::role('Administrador')->count() <= 1) {
            return back()->with('error', 'Não é possível excluir o único Administrador ativo no sistema.');
        }

        // 3. Não é permitido excluir usuário com histórico de movimentações (auditoria)
        if ($user->movements()->exists()) {
            return back()->with('error', 'Este usuário possui movimentações de estoque registradas em seu nome e não pode ser excluído para preservar o histórico e a auditoria. Você pode alterar o seu status para Inativo.');
        }

        $userName = $user->name;
        $avatarPath = $user->avatar_path;
        $user->syncRoles([]);
        $user->delete();

        if (!empty($avatarPath)) {
            $this->userAvatarService->deleteAvatar($avatarPath);
        }

        return redirect()->route('users.index')->with('success', "Usuário '{$userName}' excluído com sucesso!");
    }
}

