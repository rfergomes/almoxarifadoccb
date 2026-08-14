@extends('layouts.app')

@section('title', 'Central de Relatórios | Almoxarifado CCB')
@section('page_title', 'Central de Relatórios Gerenciais')

@section('content')
<div class="row g-3">
  <!-- Menu de Navegação dos Relatórios -->
  <div class="col-lg-3">
    <div class="card shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-journal-text text-primary me-2"></i>Tipos de Relatório</h5>
      </div>
      <div class="list-group list-group-flush">
        <a href="{{ route('reports.index', ['type' => 'inventory']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $reportType === 'inventory' ? 'active fw-bold' : '' }}">
          <span><i class="bi bi-boxes me-2"></i> Posição Geral de Estoque</span>
          <i class="bi bi-chevron-right small"></i>
        </a>
        <a href="{{ route('reports.index', ['type' => 'low_stock']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $reportType === 'low_stock' ? 'active fw-bold' : '' }}">
          <span><i class="bi bi-exclamation-triangle text-danger me-2"></i> Estoque Mínimo / Baixo</span>
          <i class="bi bi-chevron-right small"></i>
        </a>
        <a href="{{ route('reports.index', ['type' => 'overdue']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $reportType === 'overdue' ? 'active fw-bold' : '' }}">
          <span><i class="bi bi-clock-history text-warning me-2"></i> Devoluções em Atraso</span>
          <i class="bi bi-chevron-right small"></i>
        </a>
        <a href="{{ route('reports.index', ['type' => 'expiration']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $reportType === 'expiration' ? 'active fw-bold' : '' }}">
          <span><i class="bi bi-calendar-x text-danger me-2"></i> Validade de Insumos</span>
          <i class="bi bi-chevron-right small"></i>
        </a>
        <a href="{{ route('reports.index', ['type' => 'patrimony']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $reportType === 'patrimony' ? 'active fw-bold' : '' }}">
          <span><i class="bi bi-tag text-info me-2"></i> Bens & Patrimônio</span>
          <i class="bi bi-chevron-right small"></i>
        </a>
        <a href="{{ route('reports.index', ['type' => 'movements']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $reportType === 'movements' ? 'active fw-bold' : '' }}">
          <span><i class="bi bi-arrow-left-right text-info me-2"></i> Histórico de Movimentações</span>
          <i class="bi bi-chevron-right small"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Exibição do Relatório e Filtros -->
  <div class="col-lg-9">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="card-title mb-0 fw-bold">
          @if($reportType === 'inventory')
            <i class="bi bi-boxes text-primary me-2"></i>Posição Geral de Estoque
          @elseif($reportType === 'low_stock')
            <i class="bi bi-exclamation-triangle text-danger me-2"></i>Itens com Estoque Baixo
          @elseif($reportType === 'overdue')
            <i class="bi bi-clock-history text-warning me-2"></i>Empréstimos com Devolução em Atraso
          @elseif($reportType === 'expiration')
            <i class="bi bi-calendar-x text-danger me-2"></i>Controle de Validade de Insumos
          @elseif($reportType === 'patrimony')
            <i class="bi bi-tag text-info me-2"></i>Relatório de Bens e Equipamentos Patrimoniados
          @else
            <i class="bi bi-arrow-left-right text-info me-2"></i>Histórico Geral de Movimentações
          @endif
        </h5>
        
        <div class="d-flex align-items-center ms-auto gap-2">
          <div class="btn-group">
            <a href="{{ route('reports.export.pdf', request()->all()) }}" class="btn btn-danger btn-sm rounded-start-pill" target="_blank">
              <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
            </a>
            <a href="{{ route('reports.export.excel', request()->all()) }}" class="btn btn-success btn-sm rounded-end-pill">
              <i class="bi bi-file-earmark-excel me-1"></i> Exportar Excel (CSV)
            </a>
          </div>
          <a href="{{ route('user-manual.pdf') }}" class="btn btn-outline-dark btn-sm rounded-pill" target="_blank" title="Baixar Manual do Usuário (PDF)">
            <i class="bi bi-journal-bookmark me-1"></i> Manual (PDF)
          </a>
        </div>
      </div>

      <!-- Filtros -->
      <div class="card-body bg-light border-bottom py-3">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
          <input type="hidden" name="type" value="{{ $reportType }}">

          @if($reportType === 'inventory')
          <div class="col-md-5">
            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">Todas as Categorias</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          @elseif($reportType === 'expiration')
          <div class="col-md-5">
            <select name="expiration_filter" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="all" {{ request('expiration_filter') == 'all' ? 'selected' : '' }}>Todos com Data de Validade</option>
              <option value="expired" {{ request('expiration_filter') == 'expired' ? 'selected' : '' }}>🔴 Produtos Já Vencidos</option>
              <option value="expiring_soon" {{ request('expiration_filter') == 'expiring_soon' ? 'selected' : '' }}>🟡 Produtos a Vencer (Próximos 30 Dias)</option>
              <option value="valid" {{ request('expiration_filter') == 'valid' ? 'selected' : '' }}>🟢 Produtos Válidos (> 30 Dias)</option>
              <option value="no_expiration" {{ request('expiration_filter') == 'no_expiration' ? 'selected' : '' }}>⚪ Sem Data de Validade</option>
            </select>
          </div>
          @elseif($reportType === 'movements')
          <div class="col-md-3">
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" placeholder="Data Inicial">
          </div>
          <div class="col-md-3">
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" placeholder="Data Final">
          </div>
          <div class="col-md-3">
            <select name="movement_type" class="form-select form-select-sm">
              <option value="">Todos os Tipos</option>
              <option value="CONSUMPTION" {{ request('movement_type') == 'CONSUMPTION' ? 'selected' : '' }}>Consumo Geral</option>
              <option value="EPI" {{ request('movement_type') == 'EPI' ? 'selected' : '' }}>Entrega EPI</option>
              <option value="LOAN" {{ request('movement_type') == 'LOAN' ? 'selected' : '' }}>Empréstimo</option>
              <option value="ENTRY" {{ request('movement_type') == 'ENTRY' ? 'selected' : '' }}>Entrada de Estoque</option>
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter me-1"></i> Filtrar</button>
          </div>
          @endif
        </form>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          @if($reportType === 'inventory' || $reportType === 'low_stock')
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>SKU</th>
                <th>Nome do Material</th>
                <th>Categoria</th>
                <th>Estoque Atual</th>
                <th>Estoque Mínimo</th>
                <th>CA EPI</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($data as $mat)
              <tr>
                <td class="fw-bold text-navy">{{ $mat->code_sku }}</td>
                <td>{{ $mat->name }}</td>
                <td>{{ $mat->category?->name ?? 'Geral' }}</td>
                <td class="fw-bold fs-6">{{ $mat->current_stock }} {{ $mat->unit_measure }}</td>
                <td>{{ $mat->minimum_stock }} {{ $mat->unit_measure }}</td>
                <td>{{ $mat->ca_number ?? '-' }}</td>
                <td>
                  @if($mat->isStockLow())
                    <span class="badge bg-danger">ESTOQUE BAIXO</span>
                  @else
                    <span class="badge bg-success">NORMAL</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">Nenhum registro encontrado.</td>
              </tr>
              @endforelse
            </tbody>
          </table>

          @elseif($reportType === 'expiration')
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>SKU</th>
                <th>Nome do Insumo</th>
                <th>Categoria</th>
                <th>Estoque Atual</th>
                <th>Data de Validade</th>
                <th>Status de Validade</th>
              </tr>
            </thead>
            <tbody>
              @forelse($data as $mat)
              <tr>
                <td class="fw-bold text-navy">{{ $mat->code_sku }}</td>
                <td>{{ $mat->name }}</td>
                <td>{{ $mat->category?->name ?? 'Geral' }}</td>
                <td class="fw-bold fs-6">{{ $mat->current_stock }} {{ $mat->unit_measure }}</td>
                <td>{{ $mat->expiration_date?->format('d/m/Y') ?? 'Indefinida' }}</td>
                <td>
                  <span class="{{ $mat->expirationStatus()->badgeClass() }}">
                    {{ $mat->expirationStatus()->label() }}
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">Nenhum insumo encontrado para o filtro de validade selecionado.</td>
              </tr>
              @endforelse
            </tbody>
          </table>

          @elseif($reportType === 'patrimony')
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Código de Patrimônio</th>
                <th>SKU</th>
                <th>Nome do Equipamento / Ferramenta</th>
                <th>Categoria</th>
                <th>Estoque Armazenado</th>
              </tr>
            </thead>
            <tbody>
              @forelse($data as $mat)
              <tr>
                <td><span class="badge bg-info text-dark fs-6"><i class="bi bi-tag-fill me-1"></i>{{ $mat->patrimony_code }}</span></td>
                <td class="fw-bold text-navy">{{ $mat->code_sku }}</td>
                <td class="fw-bold">{{ $mat->name }}</td>
                <td>{{ $mat->category?->name ?? 'Geral' }}</td>
                <td><span class="badge bg-secondary fs-6">{{ $mat->current_stock }} {{ $mat->unit_measure }}</span></td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">Nenhum equipamento com registro de patrimônio cadastrado.</td>
              </tr>
              @endforelse
            </tbody>
          </table>

          @elseif($reportType === 'overdue')
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Código Mov.</th>
                <th>Material</th>
                <th>Beneficiário (Retirou)</th>
                <th>Destino</th>
                <th>Qtd. Pendente</th>
                <th>Previsão Retorno</th>
              </tr>
            </thead>
            <tbody>
              @forelse($data as $item)
              <tr>
                <td class="fw-bold text-navy">{{ $item->movement?->code }}</td>
                <td>{{ $item->material?->name }}</td>
                <td>{{ $item->movement?->beneficiary?->name }}</td>
                <td>{{ $item->movement?->destination?->name }}</td>
                <td><span class="badge bg-danger fs-6">{{ $item->pendingQuantity() }} {{ $item->material?->unit_measure }}</span></td>
                <td class="text-danger fw-bold"><i class="bi bi-clock me-1"></i>{{ $item->expected_return_date?->format('d/m/Y') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">Nenhum empréstimo em atraso registrado!</td>
              </tr>
              @endforelse
            </tbody>
          </table>

          @else
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Código</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Beneficiário / Documento</th>
                <th>Destino / Fornecedor</th>
                <th>Itens</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($data as $mov)
              <tr>
                <td class="fw-bold text-navy">{{ $mov->code }}</td>
                <td><small>{{ $mov->created_at->format('d/m/Y H:i') }}</small></td>
                <td><span class="badge {{ $mov->type->badgeClass() }}">{{ $mov->type->label() }}</span></td>
                <td>
                  @if($mov->type === \App\Enums\MovementType::ENTRY)
                    {{ $mov->entryDocument?->document_number }} ({{ $mov->entryDocument?->document_type?->label() }})
                  @else
                    {{ $mov->beneficiary?->name }}
                  @endif
                </td>
                <td>
                  @if($mov->type === \App\Enums\MovementType::ENTRY)
                    {{ $mov->entryDocument?->supplier_or_donor }}
                  @else
                    {{ $mov->destination?->name }}
                  @endif
                </td>
                <td><span class="badge bg-light text-dark border">{{ $mov->items->count() }} item(ns)</span></td>
                <td><span class="badge bg-secondary">{{ $mov->status->label() }}</span></td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">Nenhuma movimentação encontrada para os filtros.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
