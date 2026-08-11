<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Recuperação de Senha - Almoxarifado CCB</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
    <div style="background-color: #1a252f; padding: 25px; text-align: center; color: #ffffff;">
      <h2 style="margin: 0; font-size: 20px;">Congregação Cristã no Brasil</h2>
      <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.8;">Gestão de Almoxarifado - Recuperação de Senha</p>
    </div>

    <div style="padding: 30px;">
      <h3 style="color: #2c3e50; margin-top: 0;">A Paz de Deus, {{ $user->name }}!</h3>
      <p>Recebemos uma solicitação para redefinição de senha da sua conta no sistema de Almoxarifado.</p>
      
      <p>Para cadastrar sua nova senha, clique no botão abaixo:</p>

      <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $resetUrl }}" style="background-color: #3b82f6; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block;">Redefinir Minha Senha</a>
      </div>

      <p style="font-size: 13px; color: #64748b;">Ou copie e cole o seguinte link no seu navegador:<br>
        <a href="{{ $resetUrl }}" style="color: #3b82f6; word-break: break-all;">{{ $resetUrl }}</a>
      </p>

      <p style="font-size: 12px; color: #94a3b8; margin-top: 30px;">Se você não solicitou a recuperação de senha, desconsidere este e-mail.</p>
    </div>

    <div style="background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
      Congregação Cristã no Brasil &copy; {{ date('Y') }} &bull; Administração Nova Odessa
    </div>
  </div>
</body>
</html>
