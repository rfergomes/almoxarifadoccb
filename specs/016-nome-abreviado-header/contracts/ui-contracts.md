# UI Contracts: Formatação Abreviada do Nome do Usuário no Header

**Feature**: `016-nome-abreviado-header`
**Date**: 2026-09-08

## Navbar Component Contract (`resources/views/partials/navbar.blade.php`)

### 1. Trigger do Dropdown do Usuário (Header)

```html
<a class="nav-link dropdown-toggle d-flex align-items-center py-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
  <!-- Avatar / Iniciais -->
  ...
  
  <!-- Nome Responsivo -->
  <span class="fw-semibold text-dark d-none d-md-inline">{{ Auth::user()->short_name }}</span>
  <span class="fw-semibold text-dark d-inline d-md-none">{{ Auth::user()->first_name }}</span>

  <!-- Badge do Perfil -->
  <span class="badge bg-{{ Auth::user()->primary_role_badge ?? 'primary' }} ms-2">{{ Auth::user()->primary_role ?? 'Usuário' }}</span>
</a>
```

### 2. Cabeçalho Interno do Dropdown

```html
<li class="px-3 py-2 border-bottom bg-light">
  <small class="text-muted d-block">Conectado como</small>
  <span class="fw-bold text-dark d-block text-truncate">{{ Auth::user()->name }}</span>
</li>
```

### 3. Matriz de Comportamento Responsivo

| Dispositivo / Viewport | Breakpoint CSS | Dado Exibido no Gatilho | Exemplo |
|---|---|---|---|
| Smartphone / Mobile | `< 768px` (`d-inline d-md-none`) | `first_name` | `Rodrigo` |
| Tablet / Desktop | `≥ 768px` (`d-none d-md-inline`) | `short_name` | `Rodrigo Lima` |
| Menu Dropdown (Aberto) | Todas as resoluções | `name` (completo) | `Rodrigo Fernando Gomes Lima` |
