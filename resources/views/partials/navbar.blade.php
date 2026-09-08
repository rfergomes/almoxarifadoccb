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
        <a class="nav-link dropdown-toggle d-flex align-items-center py-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          @if(Auth::user()->hasAvatar())
            <img src="{{ Auth::user()->avatar_url }}" 
                 alt="{{ Auth::user()->name }}" 
                 class="rounded-circle border border-2 border-white shadow-sm me-2" 
                 style="width: 32px; height: 32px; object-fit: cover;">
          @else
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm me-2 fw-bold" 
                 style="width: 32px; height: 32px; font-size: 0.8rem;">
              {{ Auth::user()->initials() }}
            </div>
          @endif
          <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
          <span class="badge bg-{{ Auth::user()->primary_role_badge ?? 'primary' }} ms-2">{{ Auth::user()->primary_role ?? 'Usuário' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 200px;">
          <li class="px-3 py-2 border-bottom bg-light">
            <small class="text-muted d-block">Conectado como</small>
            <span class="fw-bold text-dark d-block text-truncate">{{ Auth::user()->name }}</span>
          </li>
          <li>
            <a href="{{ route('profile.edit') }}" class="dropdown-item py-2 d-flex align-items-center text-secondary">
              <i class="bi bi-person-gear fs-5 me-2 text-primary"></i> Meu Perfil
            </a>
          </li>
          <li><hr class="dropdown-divider my-1"></li>
          <li>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center">
                <i class="bi bi-box-arrow-right fs-5 me-2"></i> Sair do Sistema
              </button>
            </form>
          </li>
        </ul>
      </li>
      @endauth
    </ul>
  </div>
</nav>
