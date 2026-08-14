<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Almoxarifado Central CCB</title>

  <!-- Google Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- AdminLTE 4 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <style>
    body {
      background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card-login {
      border-radius: 1rem;
      border: none;
      box-shadow: 0 1rem 3rem rgba(0,0,0,0.3);
      width: 100%;
      max-width: 420px;
    }
  </style>
</head>
<body>
  <div class="card card-login bg-white p-4">
    <div class="text-center mb-4">
      <img src="{{ asset('images/CCB_Logo_preto_fundo_branco.png') }}" alt="CCB Logo" style="max-height: 75px; width: auto;" class="img-fluid mb-2">
      <h4 class="fw-bold mb-1">Almoxarifado Central</h4>
      <p class="text-muted small">Congregação Cristã no Brasil - Gestão de Estoque</p>
    </div>

    @if($errors->any())
      <div class="alert alert-danger py-2 mb-3 small">
        {{ $errors->first() }}
      </div>
    @endif

    @if(session('success'))
      <div class="alert alert-success py-2 mb-3 small">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label class="form-label small fw-bold">E-mail de Acesso</label>
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" id="inputEmail" class="form-control" placeholder="usuario@ccb.org.br" value="{{ old('email', 'admin@ccb.org.br') }}" required autofocus>
        </div>
      </div>

      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <label class="form-label small fw-bold mb-0">Senha</label>
          <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none fw-semibold">Esqueci minha senha</a>
        </div>
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" id="inputPassword" class="form-control" value="12345678" required>
        </div>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label small" for="remember">Lembrar acesso</label>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
        <i class="bi bi-box-arrow-in-right me-1"></i> Entrar no Sistema
      </button>
    </form>

    <div class="mt-4 pt-3 border-top text-center">
      <small class="text-muted d-block mb-2">Atalhos de Acesso Rápido para Teste:</small>
      <div class="d-flex justify-content-center gap-1">
        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill btn-quick-login" data-email="admin@ccb.org.br">Admin</button>
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-quick-login" data-email="almoxarife@ccb.org.br">Almoxarife</button>
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-quick-login" data-email="consulta@ccb.org.br">Consulta</button>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  @include('partials.alerts')

  <script>
    document.querySelectorAll('.btn-quick-login').forEach(btn => {
      btn.addEventListener('click', function() {
        document.getElementById('inputEmail').value = btn.dataset.email;
        document.getElementById('inputPassword').value = '12345678';
      });
    });
  </script>
</body>
</html>
