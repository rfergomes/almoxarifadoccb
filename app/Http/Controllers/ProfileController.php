<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserAvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected UserAvatarService $userAvatarService
    ) {}

    public function edit(): View
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $data = [
            'name' => $request->input('name'),
        ];

        if ($request->boolean('remove_avatar')) {
            $this->userAvatarService->deleteAvatar($user->avatar_path);
            $data['avatar_path'] = null;
        } elseif ($request->hasFile('avatar')) {
            $data['avatar_path'] = $this->userAvatarService->replaceAvatar(
                $request->file('avatar'),
                $user->avatar_path
            );
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Senha alterada com sucesso!');
    }
}
