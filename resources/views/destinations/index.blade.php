@extends('layouts.app')

@section('title', 'Destinos | Almoxarifado CCB')
@section('page_title', 'Gestão de Destinos (Casas de Oração & Obras)')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-geo-alt text-danger me-2"></i>Lista de Destinos Cadastrados</h5>
    @can('manage-destinations')
    <button class="btn btn-primary btn-sm rounded-pill ms-auto" data-bs-toggle="modal" data-bs-target="#modalCreateDestination">
      <i class="bi bi-plus-circle me-1"></i> Novo Destino
    </button>
    @endcan
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Código</th>
            <th>Nome do Destino</th>
            <th>Tipo</th>
            <th>Cidade</th>
            <th>Status</th>
            @can('manage-destinations')
            <th class="text-center">Ações</th>
            @endcan
          </tr>
        </thead>
        <tbody>
          @forelse($destinations as $dest)
          <tr>
            <td class="fw-bold">{{ $dest->code }}</td>
            <td>{{ $dest->name }}</td>
            <td>
              <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $dest->type)) }}</span>
            </td>
            <td>{{ $dest->city ?? '-' }}</td>
            <td>
              @if($dest->status)
                <span class="badge bg-success">Ativo</span>
              @else
                <span class="badge bg-danger">Inativo</span>
              @endif
            </td>
            @can('manage-destinations')
            <td class="text-center">
              <button type="button" 
                      class="btn btn-outline-primary btn-sm rounded-pill btn-edit-destination"
                      data-id="{{ $dest->id }}"
                      data-code="{{ $dest->code }}"
                      data-name="{{ $dest->name }}"
                      data-type="{{ $dest->type }}"
                      data-city="{{ $dest->city }}"
                      data-address="{{ $dest->address }}"
                      data-status="{{ $dest->status ? '1' : '0' }}"
                      title="Editar Destino">
                <i class="bi bi-pencil"></i> Editar
              </button>
            </td>
            @endcan
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">Nenhum destino cadastrado.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($destinations->hasPages())
  <div class="card-footer bg-white border-0">
    {{ $destinations->links() }}
  </div>
  @endif
</div>

@can('manage-destinations')
<!-- Modal Criar Destino -->
<div class="modal fade" id="modalCreateDestination" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('destinations.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Cadastrar Novo Destino</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Código do Setor/Relatório *</label>
          <input type="text" name="code" class="form-control" placeholder="Ex: CO-001" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nome do Destino *</label>
          <input type="text" name="name" class="form-control" placeholder="Ex: C.O. Jardim das Flores" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tipo de Destino *</label>
          <select name="type" class="form-select" required>
            <option value="casa_de_oracao">Casa de Oração</option>
            <option value="obra">Obra</option>
            <option value="administracao">Administração</option>
            <option value="outro">Outro</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Cidade</label>
          <input type="text" name="city" class="form-control" placeholder="Ex: São Paulo">
        </div>
        <div class="mb-3">
          <label class="form-label">Endereço</label>
          <input type="text" name="address" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Destino</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Destino -->
<div class="modal fade" id="modalEditDestination" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditDestination" method="POST" action="" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Destino</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Código do Setor/Relatório *</label>
          <input type="text" name="code" id="edit_dest_code" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nome do Destino *</label>
          <input type="text" name="name" id="edit_dest_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tipo de Destino *</label>
          <select name="type" id="edit_dest_type" class="form-select" required>
            <option value="casa_de_oracao">Casa de Oração</option>
            <option value="obra">Obra</option>
            <option value="administracao">Administração</option>
            <option value="outro">Outro</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Cidade</label>
          <input type="text" name="city" id="edit_dest_city" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Endereço</label>
          <input type="text" name="address" id="edit_dest_address" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" id="edit_dest_status" class="form-select">
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
    const modalEdit = new bootstrap.Modal(document.getElementById('modalEditDestination'));
    const formEdit = document.getElementById('formEditDestination');

    document.querySelectorAll('.btn-edit-destination').forEach(btn => {
      btn.addEventListener('click', function() {
        formEdit.action = `/destinations/${btn.dataset.id}`;
        document.getElementById('edit_dest_code').value = btn.dataset.code;
        document.getElementById('edit_dest_name').value = btn.dataset.name;
        document.getElementById('edit_dest_type').value = btn.dataset.type;
        document.getElementById('edit_dest_city').value = btn.dataset.city || '';
        document.getElementById('edit_dest_address').value = btn.dataset.address || '';
        document.getElementById('edit_dest_status').value = btn.dataset.status;

        modalEdit.show();
      });
    });
  });
</script>
@endpush
