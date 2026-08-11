@extends('layouts.app')

@section('title', 'Beneficiários | Almoxarifado CCB')
@section('page_title', 'Gestão de Beneficiários (Voluntários & Construtores)')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-people text-primary me-2"></i>Lista de Beneficiários Cadastrados</h5>
    @can('manage-beneficiaries')
    <button class="btn btn-primary btn-sm rounded-pill ms-auto" data-bs-toggle="modal" data-bs-target="#modalCreateBeneficiary">
      <i class="bi bi-plus-circle me-1"></i> Novo Beneficiário
    </button>
    @endcan
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Nome</th>
            <th>CPF</th>
            <th>Telefone</th>
            <th>Função / Cargo na CCB</th>
            <th>Status</th>
            @can('manage-beneficiaries')
            <th class="text-center">Ações</th>
            @endcan
          </tr>
        </thead>
        <tbody>
          @forelse($beneficiaries as $ben)
          <tr>
            <td class="fw-bold">{{ $ben->name }}</td>
            <td>{{ $ben->document_cpf ?? '-' }}</td>
            <td>{{ $ben->phone ?? '-' }}</td>
            <td><span class="badge bg-info text-dark">{{ $ben->role_in_ccb ?? 'Voluntário' }}</span></td>
            <td>
              @if($ben->status)
                <span class="badge bg-success">Ativo</span>
              @else
                <span class="badge bg-danger">Inativo</span>
              @endif
            </td>
            @can('manage-beneficiaries')
            <td class="text-center">
              <button type="button" 
                      class="btn btn-outline-primary btn-sm rounded-pill btn-edit-beneficiary"
                      data-id="{{ $ben->id }}"
                      data-name="{{ $ben->name }}"
                      data-cpf="{{ $ben->document_cpf }}"
                      data-phone="{{ $ben->phone }}"
                      data-role="{{ $ben->role_in_ccb }}"
                      data-status="{{ $ben->status ? '1' : '0' }}"
                      title="Editar Beneficiário">
                <i class="bi bi-pencil"></i> Editar
              </button>
            </td>
            @endcan
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">Nenhum beneficiário cadastrado.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($beneficiaries->hasPages())
  <div class="card-footer bg-white border-0">
    {{ $beneficiaries->links() }}
  </div>
  @endif
</div>

@can('manage-beneficiaries')
<!-- Modal Criar Beneficiário -->
<div class="modal fade" id="modalCreateBeneficiary" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('beneficiaries.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Cadastrar Novo Beneficiário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nome Completo *</label>
          <input type="text" name="name" class="form-control" placeholder="Ex: João da Silva" required>
        </div>
        <div class="mb-3">
          <label class="form-label">CPF</label>
          <input type="text" name="document_cpf" class="form-control" placeholder="000.000.000-00">
        </div>
        <div class="mb-3">
          <label class="form-label">Telefone</label>
          <input type="text" name="phone" class="form-control" placeholder="(11) 99999-9999">
        </div>
        <div class="mb-3">
          <label class="form-label">Função na CCB</label>
          <input type="text" name="role_in_ccb" class="form-control" placeholder="Ex: Voluntário, Oficial, Construtor, Pedreiro">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Beneficiário</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Beneficiário -->
<div class="modal fade" id="modalEditBeneficiary" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditBeneficiary" method="POST" action="" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Beneficiário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nome Completo *</label>
          <input type="text" name="name" id="edit_ben_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">CPF</label>
          <input type="text" name="document_cpf" id="edit_ben_cpf" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Telefone</label>
          <input type="text" name="phone" id="edit_ben_phone" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Função na CCB</label>
          <input type="text" name="role_in_ccb" id="edit_ben_role" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" id="edit_ben_status" class="form-select">
            <option value="1">Ativo</option>
            <option value="0">Inativo</option>
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
@endcan
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalEdit = new bootstrap.Modal(document.getElementById('modalEditBeneficiary'));
    const formEdit = document.getElementById('formEditBeneficiary');

    document.querySelectorAll('.btn-edit-beneficiary').forEach(btn => {
      btn.addEventListener('click', function() {
        formEdit.action = `/beneficiaries/${btn.dataset.id}`;
        document.getElementById('edit_ben_name').value = btn.dataset.name;
        document.getElementById('edit_ben_cpf').value = btn.dataset.cpf || '';
        document.getElementById('edit_ben_phone').value = btn.dataset.phone || '';
        document.getElementById('edit_ben_role').value = btn.dataset.role || '';
        document.getElementById('edit_ben_status').value = btn.dataset.status;

        modalEdit.show();
      });
    });
  });
</script>
@endpush
