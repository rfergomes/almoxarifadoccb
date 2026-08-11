<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', \App\Models\Setting::get('institution_name', 'Almoxarifado Central CCB'))</title>

  <!-- Google Fonts: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- AdminLTE 4 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
  <!-- Select2 & Select2 Bootstrap 5 Theme CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <style>
    body { font-family: 'Source Sans Pro', sans-serif; background-color: #f4f6f9; }
    .brand-link { height: 3.5rem; display: flex; align-items: center; justify-content: center; }
    .card { border-radius: 0.5rem; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
    .table th { font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; background-color: #f8f9fa; }
    .developer-link { color: #0d6efd; text-decoration: none; font-weight: 600; transition: all 0.2s ease-in-out; }
    .developer-link:hover { color: #0a58ca; text-decoration: underline; }
    .select2-container--bootstrap-5 .select2-selection { border-radius: 0.375rem; }
  </style>
  @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    @include('partials.navbar')
    @include('partials.sidebar')

    <main class="app-main py-3">
      <div class="app-content-header mb-3">
        <div class="container-fluid">
          <div class="row align-items-center">
            <div class="col-sm-6">
              <h3 class="mb-0 fw-bold">@yield('page_title', 'Painel Geral')</h3>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-end mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Início</a></li>
                @yield('breadcrumb')
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">
          @yield('content')
        </div>
      </div>
    </main>

    <footer class="app-footer bg-white border-top py-3 px-4 text-muted small">
      <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center flex-wrap justify-content-center justify-content-md-start">
          <img src="{{ asset('images/CCB_Logo_Reduzido.png') }}" alt="CCB Logo" style="height: 22px;" class="me-2 opacity-75">
          <span>
            <strong>{{ \App\Models\Setting::get('institution_name', 'CONGREGAÇÃO CRISTÃ NO BRASIL') }} &copy; {{ date('Y') }}</strong> &bull; 
            <span class="text-secondary fw-semibold">{{ \App\Models\Setting::get('administration_name', 'Gestão de Almoxarifado - Administração Nova Odessa') }}</span>
          </span>
        </div>
        <div class="text-center text-md-end">
          <span class="text-secondary">
            <i class="bi bi-code-slash text-primary me-1"></i> Desenvolvido por 
            <strong class="text-dark">Irmão Rodrigo Lima</strong> 
            <a href="mailto:{{ \App\Models\Setting::get('support_email', 'rfergomes@gmail.com') }}" class="developer-link ms-1" target="_blank" title="Enviar e-mail">
              <i class="bi bi-envelope-at me-1"></i>{{ \App\Models\Setting::get('support_email', 'rfergomes@gmail.com') }}
            </a>
          </span>
        </div>
      </div>
    </footer>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap 5 Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE 4 JS -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @include('partials.alerts')

  <script>
    function confirmAction(options) {
      Swal.fire({
        title: options.title || 'Tem certeza?',
        text: options.text || 'Esta ação não poderá ser desfeita!',
        icon: options.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: options.confirmButtonColor || '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: options.confirmButtonText || 'Sim, confirmar!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          if (typeof options.onConfirm === 'function') {
            options.onConfirm();
          } else if (options.formId) {
            document.getElementById(options.formId).submit();
          }
        }
      });
    }

    // Inicialização global de Tooltips e Select2 do Bootstrap 5
    document.addEventListener('DOMContentLoaded', function() {
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

      if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-enable').select2({
          theme: 'bootstrap-5',
          placeholder: 'Selecione uma opção...',
          allowClear: true,
          width: '100%'
        });
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
