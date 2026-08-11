@extends('layouts.app')

@section('title', 'Inventário ' . $inventory->code . ' | Almoxarifado CCB')
@section('page_title', 'Conferência de Inventário Geral Periódico')

@section('content')
<div class="row g-3">
  <!-- Informações do Inventário -->
  <div class="col-lg-12">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom">
          <div>
            <h4 class="fw-bold mb-1 text-navy">{{ $inventory->title }}</h4>
            <p class="text-muted mb-0 small">Código: <strong class="text-primary">{{ $inventory->code }}</strong> | Abertura: {{ $inventory->started_at->format('d/m/Y H:i') }}</p>
          </div>
          <div class="text-end">
            @if($inventory->isOpen())
              <span class="badge bg-primary fs-6 mb-1">Em Andamento</span>
            @elseif($inventory->isCompleted())
              <span class="badge bg-success fs-6 mb-1">Concluído</span>
              <small class="d-block text-muted">Finalizado em: {{ $inventory->completed_at?->format('d/m/Y H:i') }}</small>
            @endif
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <small class="text-muted d-block">Responsável que Realizou</small>
            <span class="fw-semibold"><i class="bi bi-person-check text-primary me-1"></i>{{ $inventory->user->name }}</span>
          </div>
          <div class="col-md-4">
            <small class="text-muted d-block">Total Materiais no Estoque</small>
            <span class="fw-bold fs-6">{{ $inventory->total_items }} itens</span>
          </div>
          <div class="col-md-4">
            <small class="text-muted d-block">Materiais Ajustados</small>
            <span class="fw-bold fs-6 text-warning">{{ $inventory->items_adjusted }} divergência(s)</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabela de Lançamento de Contagem -->
  <div class="col-lg-12">
    <form action="{{ route('inventories.save-counts', $inventory) }}" method="POST" id="formInventoryCounts">
      @csrf
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="bi bi-list-check text-success me-2"></i>Folha de Contagem Física</h5>
          <div class="ms-auto btn-group">
            <a href="{{ route('inventories.pdf', $inventory) }}" class="btn btn-outline-danger btn-sm rounded-start-pill" target="_blank">
              <i class="bi bi-file-earmark-pdf me-1"></i> Imprimir Termo PDF
            </a>
            @if($inventory->isOpen())
            <button type="submit" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-save me-1"></i> Salvar Contagens (Rascunho)
            </button>
            <button type="button" class="btn btn-success btn-sm rounded-end-pill" id="btnCompleteInventory">
              <i class="bi bi-check-circle me-1"></i> Concluir Inventário & Atualizar Saldos
            </button>
            @endif
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 15%;">SKU</th>
                  <th style="width: 35%;">Material</th>
                  <th style="width: 12%;">Saldo Sistema</th>
                  <th style="width: 15%;">Contagem Física *</th>
                  <th style="width: 13%;">Divergência</th>
                  <th style="width: 10%;">Justificativa</th>
                </tr>
              </thead>
              <tbody>
                @foreach($inventory->items as $item)
                <tr>
                  <td class="fw-bold text-navy">{{ $item->material->code_sku }}</td>
                  <td>
                    {{ $item->material->name }}
                    <small class="d-block text-muted">{{ $item->material->category?->name ?? 'Geral' }}</small>
                  </td>
                  <td class="fw-bold text-center bg-light">
                    {{ $item->system_stock }} {{ $item->material->unit_measure }}
                  </td>
                  <td>
                    @if($inventory->isOpen())
                      <input type="number" 
                             name="items[{{ $item->id }}][counted_stock]" 
                             class="form-control input-counted text-center fw-bold" 
                             value="{{ $item->counted_stock }}" 
                             data-system="{{ $item->system_stock }}" 
                             data-unit="{{ $item->material->unit_measure }}" 
                             placeholder="Ex: {{ $item->system_stock }}" min="0">
                    @else
                      <span class="fw-bold fs-6">{{ $item->counted_stock ?? '-' }} {{ $item->material->unit_measure }}</span>
                    @endif
                  </td>
                  <td class="text-center diff-cell">
                    @if($item->difference !== null)
                      @if($item->difference > 0)
                        <span class="badge bg-success">+{{ $item->difference }} (Sobra)</span>
                      @elseif($item->difference < 0)
                        <span class="badge bg-danger">{{ $item->difference }} (Falta)</span>
                      @else
                        <span class="badge bg-secondary">0 (OK)</span>
                      @endif
                    @else
                      <span class="badge bg-light text-muted border">Não contado</span>
                    @endif
                  </td>
                  <td>
                    @if($inventory->isOpen())
                      <input type="text" name="items[{{ $item->id }}][notes]" class="form-control form-control-sm" value="{{ $item->notes }}" placeholder="Observação...">
                    @else
                      <small class="text-muted">{{ $item->notes ?? '-' }}</small>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const formCounts = document.getElementById('formInventoryCounts');

    document.querySelectorAll('.input-counted').forEach(input => {
      input.addEventListener('input', function() {
        const tr = input.closest('tr');
        const diffCell = tr.querySelector('.diff-cell');
        const systemStock = parseInt(input.dataset.system);
        const unit = input.dataset.unit;
        
        if (input.value !== '') {
          const counted = parseInt(input.value);
          const diff = counted - systemStock;

          if (diff > 0) {
            diffCell.innerHTML = `<span class="badge bg-success">+${diff} (Sobra)</span>`;
          } else if (diff < 0) {
            diffCell.innerHTML = `<span class="badge bg-danger">${diff} (Falta)</span>`;
          } else {
            diffCell.innerHTML = `<span class="badge bg-secondary">0 (OK)</span>`;
          }
        } else {
          diffCell.innerHTML = `<span class="badge bg-light text-muted border">Não contado</span>`;
        }
      });
    });

    const btnComplete = document.getElementById('btnCompleteInventory');
    if (btnComplete) {
      btnComplete.addEventListener('click', function() {
        confirmAction({
          title: 'Concluir e Atualizar Saldo do Estoque?',
          text: 'Os saldos dos materiais no almoxarifado serão atualizados com base nas contagens físicas deste inventário.',
          icon: 'warning',
          confirmButtonText: 'Sim, Concluir Inventário!',
          onConfirm: function() {
            formCounts.action = "{{ route('inventories.complete', $inventory) }}";
            formCounts.submit();
          }
        });
      });
    }
  });
</script>
@endpush
