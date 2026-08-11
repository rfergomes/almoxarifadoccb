<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar Senha | Almoxarifado Central CCB</title>

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
    .card-forgot {
      border-radius: 1rem;
      border: none;
      box-shadow: 0 1rem 3rem rgba(0,0,0,0.3);
      width: 100%;
      max-width: 420px;
    }
  </style>
</head>
<body>
  <div class="card card-forgot bg-white p-4">
    <div class="text-center mb-4">
      <img src="{{ asset('images/CCB_Logo_fundo_claro.png') }}" alt="CCB Logo" style="max-height: 70px; width: auto;" class="img-fluid mb-2">
      <h4 class="fw-bold mb-1">Recuperar Senha</h4>
      <p class="text-muted small">Informe seu e-mail cadastrado para receber as instruções</p>
    </div>

    @if($errors->any())
      <div class="alert alert-danger py-2 mb-3 small">
        {{ $errors->first() }}
      </div>
    @endif

    @if(session('status_html'))
      <div class="alert alert-success py-2 mb-3 small">
        {!! session('status_html') !!}
      </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
      @csrf
      <div class="mb-4">
        <label class="form-label small fw-bold">E-mail Corporativo</label>
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" class="form-control" placeholder="seu.email@ccb.org.br" value="{{ old('email') }}" required autofocus>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill mb-3">
        <i class="bi bi-send me-1"></i> Enviar Link de Recuperação
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
