<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot_password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Informe seu e-mail de acesso.',
            'email.exists' => 'Não encontramos nenhum usuário cadastrado com este e-mail.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user->status) {
            return back()->withErrors(['email' => 'Sua conta de usuário está inativa. Entre em contato com o Administrador.']);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));
            $message = 'Enviamos um e-mail com as instruções para redefinição de sua senha.';
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de recuperação: ' . $e->getMessage());
            // Em ambiente local, permite que o link seja informado na mensagem de aviso
            $message = "Instruções registradas. Link de teste local: <a href='{$resetUrl}'>Redefinir Senha Aqui</a>";
        }

        return back()->with('status_html', $message);
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ], [
            'password.required' => 'Informe a nova senha.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $resetRecord || ! Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Este link de redefinição de senha é inválido ou expirou.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Sua senha foi redefinida com sucesso! Faça seu login com a nova senha.');
    }
}
