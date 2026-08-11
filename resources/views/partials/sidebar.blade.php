<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
  <div class="sidebar-brand text-center py-3 border-bottom border-secondary">
    <a href="{{ route('dashboard') }}" class="brand-link text-white text-decoration-none fw-bold fs-5 d-flex align-items-center justify-content-center">
      <img src="{{ asset('images/CCB_Logo_fundo_claro.png') }}" alt="CCB Logo" style="height: 38px;" class="me-2">
      <span class="brand-text fw-bold">Almoxarifado</span>
    </a>
  </div>
  <div class="sidebar-wrapper mt-2">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        @can('view-dashboard')
        <li class="nav-item">
          <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard Geral">
            <i class="nav-icon bi bi-speedometer2 text-info me-2"></i>
            <p>Dashboard</p>
          </a>
        </li>
        @endcan

        @can('view-movements')
        <li class="nav-item">
          <a href="{{ route('movements.index') }}" class="nav-link {{ request()->routeIs('movements.index') || request()->routeIs('movements.create') || request()->routeIs('movements.show') ? 'active' : '' }}" title="Saídas & Empréstimos">
            <i class="nav-icon bi bi-arrow-left-right text-success me-2"></i>
            <p>Saídas</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('entries.index') }}" class="nav-link {{ request()->routeIs('entries.*') ? 'active' : '' }}" title="Entradas por Nota Fiscal ou Doação">
            <i class="nav-icon bi bi-box-arrow-in-down text-info me-2"></i>
            <p>Entradas</p>
          </a>
        </li>
        @endcan

        @can('view-materials')
        <li class="nav-item">
          <a href="{{ route('materials.index') }}" class="nav-link {{ request()->routeIs('materials.*') ? 'active' : '' }}" title="Materiais & Saldo de Estoque">
            <i class="nav-icon bi bi-boxes text-warning me-2"></i>
            <p>Estoque</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('inventories.index') }}" class="nav-link {{ request()->routeIs('inventories.*') ? 'active' : '' }}" title="Inventário Geral Periódico">
            <i class="nav-icon bi bi-clipboard-check text-success me-2"></i>
            <p>Inventário</p>
          </a>
        </li>
        @endcan

        @can('view-beneficiaries')
        <li class="nav-item">
          <a href="{{ route('beneficiaries.index') }}" class="nav-link {{ request()->routeIs('beneficiaries.*') ? 'active' : '' }}" title="Cadastro de Beneficiários">
            <i class="nav-icon bi bi-people text-primary me-2"></i>
            <p>Beneficiários</p>
          </a>
        </li>
        @endcan

        @can('view-destinations')
        <li class="nav-item">
          <a href="{{ route('destinations.index') }}" class="nav-link {{ request()->routeIs('destinations.*') ? 'active' : '' }}" title="Destinos (Casas de Oração / Obras)">
            <i class="nav-icon bi bi-geo-alt text-danger me-2"></i>
            <p>Localidades</p>
          </a>
        </li>
        @endcan

        @can('view-dashboard')
        <li class="nav-item">
          <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" title="Central de Relatórios Gerenciais">
            <i class="nav-icon bi bi-file-earmark-bar-graph text-warning me-2"></i>
            <p>Relatórios</p>
          </a>
        </li>
        @endcan

        @can('manage-users')
        <li class="nav-item border-top border-secondary my-1 pt-1">
          <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" title="Gestão de Usuários">
            <i class="nav-icon bi bi-shield-lock text-danger me-2"></i>
            <p>Usuários</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" title="Configurações do Sistema">
            <i class="nav-icon bi bi-gear text-warning me-2"></i>
            <p>Configurações</p>
          </a>
        </li>
        @endcan
      </ul>
    </nav>
  </div>
</aside>
