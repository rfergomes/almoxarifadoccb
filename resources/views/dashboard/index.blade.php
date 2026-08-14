@extends('layouts.app')

@section('title', 'Dashboard | Almoxarifado CCB')
@section('page_title', 'Painel de Indicadores & Alertas em Tempo Real')

@section('content')
<!-- Cartões de KPI -->
<div class="row g-3 mb-4">
  <div class="col-lg-3 col-6">
    <div class="card bg-primary text-white shadow-sm h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h3 class="fw-bold mb-0">{{ $totalStockItems }}</h3>
          <span class="fs-7 text-white-50">Itens em Estoque</span>
        </div>
        <i class="bi bi-boxes fs-1 text-white-50"></i>
      </div>
      <div class="card-footer bg-black bg-opacity-10 border-0 text-center py-2">
        <a href="{{ route('materials.index') }}" class="text-white text-decoration-none small">Ver Materiais <i class="bi bi-arrow-right-circle"></i></a>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="card bg-danger text-white shadow-sm h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h3 class="fw-bold mb-0">{{ $expiredMaterialsCount }}</h3>
          <span class="fs-7 text-white-50">Produtos Vencidos</span>
        </div>
        <i class="bi bi-calendar-x fs-1 text-white-50"></i>
      </div>
      <div class="card-footer bg-black bg-opacity-10 border-0 text-center py-2">
        <a href="{{ route('materials.index', ['expiration_status' => 'expired']) }}" class="text-white text-decoration-none small">Ver Vencidos <i class="bi bi-arrow-right-circle"></i></a>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="card bg-warning text-dark shadow-sm h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h3 class="fw-bold mb-0">{{ $expiringSoonMaterialsCount }}</h3>
          <span class="fs-7 text-dark-50">Produtos a Vencer (30d)</span>
        </div>
        <i class="bi bi-calendar-event fs-1 text-dark-50"></i>
      </div>
      <div class="card-footer bg-black bg-opacity-10 border-0 text-center py-2">
        <a href="{{ route('materials.index', ['expiration_status' => 'expiring_soon']) }}" class="text-dark text-decoration-none small">Ver a Vencer <i class="bi bi-arrow-right-circle"></i></a>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="card bg-info text-white shadow-sm h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h3 class="fw-bold mb-0">{{ $patrimonyMaterialsCount }}</h3>
          <span class="fs-7 text-white-50">Itens com Patrimônio</span>
        </div>
        <i class="bi bi-tag fs-1 text-white-50"></i>
      </div>
      <div class="card-footer bg-black bg-opacity-10 border-0 text-center py-2">
        <a href="{{ route('materials.index', ['has_patrimony' => '1']) }}" class="text-white text-decoration-none small">Ver Patrimônios <i class="bi bi-arrow-right-circle"></i></a>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Tabela Empréstimos em Atraso -->
  <div class="col-lg-6" id="sectionOverdue">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold text-danger">
          <i class="bi bi-clock-history me-2"></i>Empréstimos em Atraso (Requer Cobrança)
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Item / Material</th>
                <th>Beneficiário</th>
                <th>Prev. Retorno</th>
                <th>Pendente</th>
              </tr>
            </thead>
            <tbody>
              @forelse($overdueItems as $item)
              <tr>
                <td class="fw-bold">{{ $item->material?->name }}</td>
                <td>
                  <div>{{ $item->movement?->beneficiary?->name }}</div>
                  <small class="text-muted">{{ $item->movement?->destination?->name }}</small>
                </td>
                <td class="text-danger fw-bold">{{ $item->expected_return_date?->format('d/m/Y') }}</td>
                <td><span class="badge bg-danger">{{ $item->pendingQuantity() }} {{ $item->material?->unit_measure }}</span></td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-3 text-muted">Nenhum empréstimo em atraso! 🎉</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabela Estoque Mínimo -->
  <div class="col-lg-6" id="sectionLowStock">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold text-warning text-dark">
          <i class="bi bi-exclamation-triangle me-2"></i>Materiais Abaixo do Estoque Mínimo
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>SKU</th>
                <th>Material</th>
                <th>Estoque Atual</th>
                <th>Estoque Mínimo</th>
              </tr>
            </thead>
            <tbody>
              @forelse($lowStockMaterials as $mat)
              <tr>
                <td class="fw-bold">{{ $mat->code_sku }}</td>
                <td>{{ $mat->name }}</td>
                <td class="text-danger fw-bold fs-6">{{ $mat->current_stock }} {{ $mat->unit_measure }}</td>
                <td class="text-muted">{{ $mat->minimum_stock }} {{ $mat->unit_measure }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-3 text-muted">Todos os saldos estão normais!</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Últimas Movimentações -->
  <div class="col-lg-12">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-arrow-left-right text-success me-2"></i>Últimas Movimentações Registradas</h5>
        <a href="{{ route('movements.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">Ver Todas</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Código</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Beneficiário</th>
                <th>Destino</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentMovements as $mov)
              <tr>
                <td class="fw-bold"><a href="{{ route('movements.show', $mov) }}">{{ $mov->code }}</a></td>
                <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                <td><span class="badge {{ $mov->type->badgeClass() }}">{{ $mov->type->label() }}</span></td>
                <td>{{ $mov->beneficiary?->name }}</td>
                <td>{{ $mov->destination?->name }}</td>
                <td><span class="badge {{ $mov->status->badgeClass() }}">{{ $mov->status->label() }}</span></td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-3 text-muted">Nenhuma movimentação recente.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
