<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Credenciais de Acesso ao Almoxarifado CCB</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
    <div style="background-color: #1a252f; padding: 25px; text-align: center; color: #ffffff;">
      <h2 style="margin: 0; font-size: 20px;">Congregação Cristã no Brasil</h2>
      <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.8;">Gestão de Almoxarifado - Administração Nova Odessa</p>
    </div>

    <div style="padding: 30px;">
      <h3 style="color: #2c3e50; margin-top: 0;">A Paz de Deus, {{ $user->name }}!</h3>
      <p>Sua conta de usuário foi criada no sistema de Gestão de Almoxarifado da CCB.</p>
      
      <div style="background-color: #f8fafc; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0 0 8px 0;"><strong>E-mail de Acesso:</strong> {{ $user->email }}</p>
        <p style="margin: 0;"><strong>Senha Inicial:</strong> <code style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 15px;">{{ $plainPassword }}</code></p>
      </div>

      <p>Para acessar o sistema e realizar seu login, clique no botão abaixo:</p>

      <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('login') }}" style="background-color: #10b981; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block;">Acessar o Sistema de Almoxarifado</a>
      </div>

      <p style="font-size: 12px; color: #64748b; margin-top: 30px;">Recomendamos alterar sua senha após o primeiro acesso.</p>
    </div>

    <div style="background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
      Congregação Cristã no Brasil &copy; {{ date('Y') }} &bull; Administração Nova Odessa
    </div>
  </div>
</body>
</html>
