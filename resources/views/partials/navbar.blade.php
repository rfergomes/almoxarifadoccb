<nav class="app-header navbar navbar-expand bg-white border-bottom shadow-sm">
  <div class="container-fluid">
    <ul class="navbar-nav align-items-center">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list fs-4"></i></a>
      </li>
      <li class="nav-item d-none d-md-inline-block ms-2">
        <a href="{{ route('dashboard') }}" class="nav-link fw-bold text-navy d-flex align-items-center">
          <img src="{{ asset('images/CCB_Logo_Reduzido.png') }}" alt="CCB Logo" style="height: 32px;" class="me-2">
          <span>Administração - Nova Odessa</span>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ms-auto align-items-center">
      @auth
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle fs-5 me-1 text-primary"></i>
          <span class="fw-semibold">{{ Auth::user()->name }}</span>
          <span class="badge bg-primary ms-2">{{ Auth::user()->roles->first()?->name ?? 'Usuário' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
          <li>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                <i class="bi bi-box-arrow-right me-2"></i> Sair do Sistema
              </button>
            </form>
          </li>
        </ul>
      </li>
      @endauth
    </ul>
  </div>
</nav>
