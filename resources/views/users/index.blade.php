@extends('layouts.app')

@section('title', 'Gestão de Usuários | Almoxarifado CCB')
@section('page_title', 'Gerenciador de Usuários e Permissões de Acesso')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-shield-lock text-danger me-2"></i>Usuários do Sistema</h5>
    <button class="btn btn-primary btn-sm rounded-pill ms-auto" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
      <i class="bi bi-person-plus me-1"></i> Novo Usuário
    </button>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Nome do Usuário</th>
            <th>E-mail Corporativo</th>
            <th>Perfil / Nível de Acesso</th>
            <th>Status</th>
            <th>Data de Cadastro</th>
            <th class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $usr)
          <tr>
            <td class="fw-bold">
              <i class="bi bi-person-circle text-primary me-2"></i>{{ $usr->name }}
            </td>
            <td>{{ $usr->email }}</td>
            <td>
              @foreach($usr->roles as $role)
                @if($role->name === 'Administrador')
                  <span class="badge bg-danger fs-6 role-badge" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        data-bs-custom-class="custom-tooltip"
                        title="Administrador: Acesso total ao sistema, gestão de usuários, relatórios gerenciais, auditorias e todos os cadastros.">
                    <i class="bi bi-shield-fill-check me-1"></i>Administrador
                  </span>
                @elseif($role->name === 'Almoxarife')
                  <span class="badge bg-primary fs-6 role-badge" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        data-bs-custom-class="custom-tooltip"
                        title="Almoxarife: Operações do dia a dia: lança saídas, empréstimos, devoluções, entradas por NF/doação e cadastra materiais/beneficiários.">
                    <i class="bi bi-box-seam me-1"></i>Almoxarife
                  </span>
                @else
                  <span class="badge bg-secondary fs-6 role-badge" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        data-bs-custom-class="custom-tooltip"
                        title="Consulta: Acesso somente de leitura: visualiza relatórios, movimentações, estoque e cadastros sem permissão para alteração.">
                    <i class="bi bi-eye me-1"></i>Consulta
                  </span>
                @endif
              @endforeach
            </td>
            <td>
              @if($usr->status)
                <span class="badge bg-success">Ativo</span>
              @else
                <span class="badge bg-danger">Inativo</span>
              @endif
            </td>
            <td><small class="text-muted">{{ $usr->created_at->format('d/m/Y H:i') }}</small></td>
            <td class="text-center">
              <div class="btn-group">
                <button type="button" 
                        class="btn btn-outline-primary btn-sm rounded-start-pill btn-edit-user"
                        data-id="{{ $usr->id }}"
                        data-name="{{ $usr->name }}"
                        data-email="{{ $usr->email }}"
                        data-role="{{ $usr->roles->first()?->name }}"
                        data-status="{{ $usr->status ? '1' : '0' }}"
                        title="Editar Perfil / Dados">
                  <i class="bi bi-pencil"></i> Editar
                </button>
                <button type="button" 
                        class="btn btn-outline-warning btn-sm rounded-end-pill btn-reset-user-password"
                        data-id="{{ $usr->id }}"
                        data-name="{{ $usr->name }}"
                        title="Redefinir Senha">
                  <i class="bi bi-key"></i> Senha
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">Nenhum usuário cadastrado.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($users->hasPages())
  <div class="card-footer bg-white border-0">
    {{ $users->links() }}
  </div>
  @endif
</div>

<!-- Modal Criar Usuário -->
<div class="modal fade" id="modalCreateUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('users.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-person-plus text-primary me-2"></i>Cadastrar Novo Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Nome Completo *</label>
          <input type="text" name="name" class="form-control" placeholder="Ex: Irmão Daniel Oliveira" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">E-mail Corporativo *</label>
          <input type="email" name="email" class="form-control" placeholder="Ex: daniel.oliveira@ccb.org.br" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Perfil / Nível de Acesso *</label>
          <select name="role" class="form-select" required>
            <option value="">Selecione o perfil...</option>
            <option value="Administrador">Administrador - (Gestão Total do Sistema)</option>
            <option value="Almoxarife">Almoxarife - (Lança Entradas/Saídas/Cadastros)</option>
            <option value="Consulta">Consulta - (Apenas Leitura / Visualização)</option>
          </select>
          <small class="text-muted d-block mt-1">Passe o mouse sobre os badges na tabela para ver a descrição detalhada de cada perfil.</small>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Senha Inicial *</label>
          <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Confirmar Senha *</label>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a senha" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Status do Usuário</label>
          <select name="status" class="form-select">
            <option value="1">Ativo (Acesso Liberado)</option>
            <option value="0">Inativo (Acesso Bloqueado)</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Usuário</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Usuário -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditUser" method="POST" action="" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Nome Completo *</label>
          <input type="text" name="name" id="edit_user_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">E-mail Corporativo *</label>
          <input type="email" name="email" id="edit_user_email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Perfil / Nível de Acesso *</label>
          <select name="role" id="edit_user_role" class="form-select" required>
            <option value="Administrador">Administrador - (Gestão Total do Sistema)</option>
            <option value="Almoxarife">Almoxarife - (Lança Entradas/Saídas/Cadastros)</option>
            <option value="Consulta">Consulta - (Apenas Leitura / Visualização)</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Status do Usuário</label>
          <select name="status" id="edit_user_status" class="form-select">
            <option value="1">Ativo (Acesso Liberado)</option>
            <option value="0">Inativo (Acesso Bloqueado)</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Redefinir Senha -->
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formResetPassword" method="POST" action="" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-key text-warning me-2"></i>Redefinir Senha do Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Usuário: <strong id="resetUserName">-</strong></p>
        <div class="mb-3">
          <label class="form-label fw-semibold">Nova Senha *</label>
          <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Confirmar Nova Senha *</label>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a nova senha" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Redefinir Senha</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalEdit = new bootstrap.Modal(document.getElementById('modalEditUser'));
    const formEdit = document.getElementById('formEditUser');

    document.querySelectorAll('.btn-edit-user').forEach(btn => {
      btn.addEventListener('click', function() {
        formEdit.action = `/users/${btn.dataset.id}`;
        document.getElementById('edit_user_name').value = btn.dataset.name;
        document.getElementById('edit_user_email').value = btn.dataset.email;
        document.getElementById('edit_user_role').value = btn.dataset.role;
        document.getElementById('edit_user_status').value = btn.dataset.status;

        modalEdit.show();
      });
    });

    const modalReset = new bootstrap.Modal(document.getElementById('modalResetPassword'));
    const formReset = document.getElementById('formResetPassword');

    document.querySelectorAll('.btn-reset-user-password').forEach(btn => {
      btn.addEventListener('click', function() {
        formReset.action = `/users/${btn.dataset.id}/reset-password`;
        document.getElementById('resetUserName').textContent = btn.dataset.name;

        modalReset.show();
      });
    });
  });
</script>
@endpush
