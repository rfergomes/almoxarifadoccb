@extends('layouts.app')

@section('title', 'Materiais | Almoxarifado CCB')
@section('page_title', 'Catálogo de Materiais & Saldo de Estoque')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <h5 class="card-title mb-0 fw-bold"><i class="bi bi-boxes text-warning me-2"></i>Materiais Cadastrados</h5>
      @can('manage-materials')
      <button class="btn btn-primary btn-sm rounded-pill ms-auto" data-bs-toggle="modal" data-bs-target="#modalCreateMaterial">
        <i class="bi bi-plus-circle me-1"></i> Novo Material
      </button>
      @endcan
    </div>

    <!-- Barra de Filtros e Busca -->
    <form method="GET" action="{{ route('materials.index') }}" class="row g-2 align-items-center">
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Buscar por Nome, SKU ou Patrimônio..." value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-md-3">
        <select name="category_id" class="form-select form-select-sm">
          <option value="">Todas as Categorias</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="expiration_status" class="form-select form-select-sm">
          <option value="">Status de Validade (Todos)</option>
          <option value="expired" {{ request('expiration_status') == 'expired' ? 'selected' : '' }}>🔴 Vencidos</option>
          <option value="expiring_soon" {{ request('expiration_status') == 'expiring_soon' ? 'selected' : '' }}>🟡 Próximos de Vencer (30d)</option>
          <option value="valid" {{ request('expiration_status') == 'valid' ? 'selected' : '' }}>🟢 Válidos</option>
          <option value="no_expiration" {{ request('expiration_status') == 'no_expiration' ? 'selected' : '' }}>⚪ Sem Validade</option>
        </select>
      </div>
      <div class="col-md-2">
        <select name="has_patrimony" class="form-select form-select-sm">
          <option value="">Patrimônio (Todos)</option>
          <option value="1" {{ request('has_patrimony') == '1' ? 'selected' : '' }}>🔵 Com Patrimônio</option>
        </select>
      </div>
      <div class="col-md-1 d-flex gap-1">
        <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-funnel"></i> Filtrar</button>
        @if(request()->anyFilled(['search', 'category_id', 'expiration_status', 'has_patrimony']))
          <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpar Filtros"><i class="bi bi-x-circle"></i></a>
        @endif
      </div>
    </form>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>SKU</th>
            <th>Nome do Material</th>
            <th>Patrimônio</th>
            <th>Categoria</th>
            <th>Estoque Atual</th>
            <th>Validade Produto</th>
            <th>CA (EPI)</th>
            <th>Retornável?</th>
            <th>Status</th>
            @can('manage-materials')
            <th class="text-center">Ações</th>
            @endcan
          </tr>
        </thead>
        <tbody>
          @forelse($materials as $mat)
          <tr>
            <td class="fw-bold text-navy">{{ $mat->code_sku }}</td>
            <td>{{ $mat->name }}</td>
            <td>
              @if($mat->hasPatrimony())
                <span class="badge bg-info text-dark" title="Código de Patrimônio da Entidade">
                  <i class="bi bi-tag-fill me-1"></i>{{ $mat->patrimony_code }}
                </span>
              @else
                <span class="text-muted small">-</span>
              @endif
            </td>
            <td>
              <span class="badge bg-secondary">{{ $mat->category?->name ?? 'Geral' }}</span>
            </td>
            <td>
              <span class="fw-bold fs-6 {{ $mat->isStockLow() ? 'text-danger' : 'text-success' }}">
                {{ $mat->current_stock }} {{ $mat->unit_measure }}
              </span>
              @if($mat->isStockLow())
                <i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="Estoque abaixo do mínimo (Mín: {{ $mat->minimum_stock }})"></i>
              @endif
            </td>
            <td>
              @if($mat->expiration_date)
                <small class="d-block fw-bold">{{ $mat->expiration_date->format('d/m/Y') }}</small>
                <span class="{{ $mat->expirationStatus()->badgeClass() }}">
                  {{ $mat->expirationStatus()->label() }}
                </span>
              @else
                <span class="badge bg-light text-dark">Indefinida</span>
              @endif
            </td>
            <td>
              @if($mat->isEpi())
                <span class="badge bg-dark">{{ $mat->ca_number ?? 'S/N' }}</span>
                @if($mat->ca_validity)
                  <small class="d-block text-muted">Val: {{ $mat->ca_validity->format('d/m/Y') }}</small>
                  @if($mat->isCaExpired())
                    <span class="badge bg-danger">Vencido!</span>
                  @endif
                @endif
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              @if($mat->is_returnable)
                <span class="badge bg-primary">Sim (Devolução)</span>
              @else
                <span class="badge bg-light text-dark">Não (Consumo)</span>
              @endif
            </td>
            <td>
              @if($mat->status)
                <span class="badge bg-success">Ativo</span>
              @else
                <span class="badge bg-danger">Inativo</span>
              @endif
            </td>
            @can('manage-materials')
            <td class="text-center">
              <div class="btn-group">
                <button type="button" 
                        class="btn btn-outline-primary btn-sm rounded-start-pill btn-edit-material"
                        data-id="{{ $mat->id }}"
                        data-sku="{{ $mat->code_sku }}"
                        data-name="{{ $mat->name }}"
                        data-category="{{ $mat->category_id }}"
                        data-unit="{{ $mat->unit_measure }}"
                        data-returnable="{{ $mat->is_returnable ? '1' : '0' }}"
                        data-min="{{ $mat->minimum_stock }}"
                        data-ca="{{ $mat->ca_number }}"
                        data-ca-validity="{{ $mat->ca_validity?->format('Y-m-d') }}"
                        data-expiration="{{ $mat->expiration_date?->format('Y-m-d') }}"
                        data-patrimony="{{ $mat->patrimony_code }}"
                        data-status="{{ $mat->status ? '1' : '0' }}"
                        title="Editar Cadastro do Material">
                  <i class="bi bi-pencil"></i> Editar
                </button>
                <button type="button" 
                        class="btn btn-outline-warning btn-sm rounded-end-pill btn-adjust-material"
                        data-id="{{ $mat->id }}"
                        data-name="{{ $mat->name }}"
                        data-current="{{ $mat->current_stock }}"
                        data-unit="{{ $mat->unit_measure }}"
                        title="Ajustar Saldo de Inventário">
                  <i class="bi bi-sliders"></i> Inventário
                </button>
              </div>
            </td>
            @endcan
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center py-4 text-muted">Nenhum material encontrado com os critérios selecionados.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($materials->hasPages())
  <div class="card-footer bg-white border-0">
    {{ $materials->links() }}
  </div>
  @endif
</div>

@can('manage-materials')
<!-- Modal Criar Material -->
<div class="modal fade" id="modalCreateMaterial" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('materials.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Cadastrar Novo Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Código SKU *</label>
            <input type="text" name="code_sku" class="form-control" placeholder="Ex: MAT-001" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nome do Material *</label>
            <input type="text" name="name" class="form-control" placeholder="Ex: Tinta Acrílica Branca 18L" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Categoria *</label>
            <select name="category_id" class="form-select" required>
              <option value="">Selecione uma categoria...</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Unidade Medida *</label>
            <input type="text" name="unit_measure" class="form-control" placeholder="UN, KG, CX, M" value="UN" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">É Retornável?</label>
            <select name="is_returnable" class="form-select">
              <option value="0">Não (Consumo)</option>
              <option value="1">Sim (Ferramenta/Eqp)</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Data de Validade (Perecíveis)</label>
            <input type="date" name="expiration_date" class="form-control">
            <small class="text-muted">Ex: Tintas, massa corrida, grafiato, colas</small>
          </div>
          <div class="col-md-6">
            <label class="form-label">Código de Patrimônio (Entidade)</label>
            <input type="text" name="patrimony_code" class="form-control" placeholder="Ex: PAT-CCB-001">
            <small class="text-muted">Exclusivo para bens/ferramentas da entidade</small>
          </div>
          <div class="col-md-6">
            <label class="form-label">Estoque Inicial *</label>
            <input type="number" name="current_stock" class="form-control" value="0" min="0" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Estoque Mínimo *</label>
            <input type="number" name="minimum_stock" class="form-control" value="5" min="0" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nº CA (Exclusivo EPI)</label>
            <input type="text" name="ca_number" class="form-control" placeholder="Ex: CA 12345">
          </div>
          <div class="col-md-6">
            <label class="form-label">Validade CA (Exclusivo EPI)</label>
            <input type="date" name="ca_validity" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Material</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Material -->
<div class="modal fade" id="modalEditMaterial" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="formEditMaterial" method="POST" action="" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Cadastro do Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info py-2 small mb-3">
          <i class="bi bi-info-circle me-1"></i> O saldo de estoque atual não é alterado no cadastro. Para acertos de inventário, utilize a ação <strong>"Inventário"</strong>.
        </div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Código SKU *</label>
            <input type="text" name="code_sku" id="edit_code_sku" class="form-control" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nome do Material *</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Categoria *</label>
            <select name="category_id" id="edit_category_id" class="form-select" required>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Unidade Medida *</label>
            <input type="text" name="unit_measure" id="edit_unit_measure" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">É Retornável?</label>
            <select name="is_returnable" id="edit_is_returnable" class="form-select">
              <option value="0">Não (Consumo)</option>
              <option value="1">Sim (Ferramenta/Eqp)</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Data de Validade (Perecíveis)</label>
            <input type="date" name="expiration_date" id="edit_expiration_date" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Código de Patrimônio (Entidade)</label>
            <input type="text" name="patrimony_code" id="edit_patrimony_code" class="form-control" placeholder="Ex: PAT-CCB-001">
          </div>
          <div class="col-md-6">
            <label class="form-label">Estoque Mínimo *</label>
            <input type="number" name="minimum_stock" id="edit_minimum_stock" class="form-control" min="0" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status do Cadastro</label>
            <select name="status" id="edit_status" class="form-select">
              <option value="1">Ativo</option>
              <option value="0">Inativo</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nº CA (Exclusivo EPI)</label>
            <input type="text" name="ca_number" id="edit_ca_number" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Validade CA (Exclusivo EPI)</label>
            <input type="date" name="ca_validity" id="edit_ca_validity" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ajuste de Estoque / Inventário -->
<div class="modal fade" id="modalAdjustStock" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formAdjustStock" method="POST" action="" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-sliders text-warning me-2"></i>Ajuste de Estoque / Inventário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">Material: <strong id="adjustMaterialName">-</strong></p>
        <p class="mb-3 text-muted small">Saldo Atual no Sistema: <span id="adjustCurrentStock" class="badge bg-secondary">-</span></p>

        <div class="mb-3">
          <label class="form-label fw-semibold">Novo Saldo Contado (Físico) *</label>
          <input type="number" name="new_stock" id="inputNewStock" class="form-control" min="0" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Justificativa do Ajuste / Inventário *</label>
          <textarea name="justification" class="form-control" rows="3" placeholder="Ex: Contagem física de inventário mensal realizada pela administração." required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Confirmar Ajuste de Saldo</button>
      </div>
    </form>
  </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalEdit = new bootstrap.Modal(document.getElementById('modalEditMaterial'));
    const formEdit = document.getElementById('formEditMaterial');

    document.querySelectorAll('.btn-edit-material').forEach(btn => {
      btn.addEventListener('click', function() {
        formEdit.action = `/materials/${btn.dataset.id}`;
        document.getElementById('edit_code_sku').value = btn.dataset.sku;
        document.getElementById('edit_name').value = btn.dataset.name;
        document.getElementById('edit_category_id').value = btn.dataset.category;
        document.getElementById('edit_unit_measure').value = btn.dataset.unit;
        document.getElementById('edit_is_returnable').value = btn.dataset.returnable;
        document.getElementById('edit_minimum_stock').value = btn.dataset.min;
        document.getElementById('edit_status').value = btn.dataset.status;
        document.getElementById('edit_ca_number').value = btn.dataset.ca || '';
        document.getElementById('edit_ca_validity').value = btn.dataset.caValidity || '';
        document.getElementById('edit_expiration_date').value = btn.dataset.expiration || '';
        document.getElementById('edit_patrimony_code').value = btn.dataset.patrimony || '';

        modalEdit.show();
      });
    });

    const modalAdjust = new bootstrap.Modal(document.getElementById('modalAdjustStock'));
    const formAdjust = document.getElementById('formAdjustStock');

    document.querySelectorAll('.btn-adjust-material').forEach(btn => {
      btn.addEventListener('click', function() {
        formAdjust.action = `/materials/${btn.dataset.id}/adjust-stock`;
        document.getElementById('adjustMaterialName').textContent = btn.dataset.name;
        document.getElementById('adjustCurrentStock').textContent = `${btn.dataset.current} ${btn.dataset.unit}`;
        document.getElementById('inputNewStock').value = btn.dataset.current;

        modalAdjust.show();
      });
    });
  });
</script>
@endpush

