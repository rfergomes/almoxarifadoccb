<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cadastrar Nova Senha | Almoxarifado Central CCB</title>

  <!-- Google Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- AdminLTE 4 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">

  <style>
    body {
      background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card-reset {
      border-radius: 1rem;
      border: none;
      box-shadow: 0 1rem 3rem rgba(0,0,0,0.3);
      width: 100%;
      max-width: 420px;
    }
  </style>
</head>
<body>
  <div class="card card-reset bg-white p-4">
    <div class="text-center mb-4">
      <img src="{{ asset('images/CCB_Logo_fundo_claro.png') }}" alt="CCB Logo" style="max-height: 70px; width: auto;" class="img-fluid mb-2">
      <h4 class="fw-bold mb-1">Cadastrar Nova Senha</h4>
      <p class="text-muted small">Crie uma nova senha de acesso segura para sua conta</p>
    </div>

    @if($errors->any())
      <div class="alert alert-danger py-2 mb-3 small">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="mb-3">
        <label class="form-label small fw-bold">E-mail de Acesso</label>
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" class="form-control bg-light" value="{{ old('email', $email) }}" required readonly>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label small fw-bold">Nova Senha *</label>
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required autofocus>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label small fw-bold">Confirmar Nova Senha *</label>
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a nova senha" required>
        </div>
      </div>

      <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-pill mb-3">
        <i class="bi bi-check-circle me-1"></i> Salvar Nova Senha e Entrar
      </button>

      <div class="text-center">
        <a href="{{ route('login') }}" class="text-decoration-none small text-secondary">
          <i class="bi bi-arrow-left me-1"></i> Voltar para a tela de login
        </a>
      </div>
    </form>
  </div>
</body>
</html>
