@extends('layouts.app')

@section('title', 'Comprovante ' . $movement->code . ' | Almoxarifado CCB')
@section('page_title', \App\Models\Setting::get('receipt_header_title', 'Comprovante de Movimentação de Estoque'))

@push('styles')
<style>
  @media print {
    @page {
      size: A4 portrait;
      margin: 8mm 10mm;
    }

    /* Reset estrito dos wrappers e do layout-fixed do AdminLTE 4 */
    html,
    body,
    body.layout-fixed,
    .app-wrapper,
    .layout-fixed .app-wrapper,
    .app-main-wrapper,
    .layout-fixed .app-main-wrapper,
    .app-main,
    .layout-fixed .app-main,
    .sidebar-expand-sm.layout-fixed .app-main,
    .sidebar-expand-md.layout-fixed .app-main,
    .sidebar-expand-lg.layout-fixed .app-main,
    .sidebar-expand-xl.layout-fixed .app-main,
    .sidebar-expand-xxl.layout-fixed .app-main,
    .sidebar-expand.layout-fixed .app-main,
    .app-content,
    .container-fluid,
    .row.justify-content-center,
    .col-lg-10 {
      background-color: #ffffff !important;
      background: #ffffff !important;
      font-size: 12px !important;
      color: #000000 !important;
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      min-width: 0 !important;
      max-width: 100% !important;
      overflow: visible !important;
      height: auto !important;
      min-height: 0 !important;
      position: static !important;
      display: block !important;
      float: none !important;
      grid-template-areas: none !important;
      grid-template-columns: none !important;
      grid-template-rows: none !important;
      grid-gap: 0 !important;
      flex: none !important;
      transform: none !important;
    }

    /* Ocultação rigorosa de elementos não imprimíveis */
    .app-sidebar,
    .app-header,
    .app-footer,
    .app-content-header,
    .sidebar,
    .navbar,
    .btn,
    .alert,
    .modal,
    .no-print,
    .btn-group {
      display: none !important;
    }

    /* Contêiner Oficial do Comprovante */
    .card.printable-receipt,
    .printable-receipt {
      border: none !important;
      box-shadow: none !important;
      padding: 0 !important;
      margin: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      min-width: 0 !important;
      background: transparent !important;
      display: block !important;
    }

    .printable-receipt .card-body {
      padding: 0 !important;
    }

    /* Preservação e Diagramação do Grid Interno */
    .printable-receipt .row {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      margin-left: -0.5rem !important;
      margin-right: -0.5rem !important;
      width: auto !important;
      float: none !important;
    }

    .printable-receipt [class*="col-"] {
      display: block !important;
      float: none !important;
      padding-left: 0.5rem !important;
      padding-right: 0.5rem !important;
      box-sizing: border-box !important;
    }

    .printable-receipt .col-12 {
      width: 100% !important;
      flex: 0 0 100% !important;
      max-width: 100% !important;
    }

    .printable-receipt .col-6 {
      width: 50% !important;
      flex: 0 0 50% !important;
      max-width: 50% !important;
    }

    .printable-receipt .col-md-3,
    .printable-receipt .col-3 {
      width: 25% !important;
      flex: 0 0 25% !important;
      max-width: 25% !important;
    }

    .printable-receipt .col-md-4,
    .printable-receipt .col-4 {
      width: 33.333333% !important;
      flex: 0 0 33.333333% !important;
      max-width: 33.333333% !important;
    }

    /* Tabela de Itens */
    .table-responsive {
      overflow: visible !important;
      display: block !important;
      width: 100% !important;
      margin: 0 0 15px 0 !important;
    }

    .table {
      font-size: 11px !important;
      width: 100% !important;
      margin-bottom: 15px !important;
      border-collapse: collapse !important;
    }

    .table th,
    .table td {
      padding: 4px 6px !important;
      color: #000000 !important;
      -webkit-text-fill-color: #000000 !important;
      border: 1px solid #000000 !important;
    }

    .table thead {
      display: table-header-group !important;
    }

    .table thead th {
      border-bottom: 2px solid #000000 !important;
      font-weight: 700 !important;
      color: #000000 !important;
    }

    .table tr {
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }

    /* Estilos Defensivos Monocromáticos (Visíveis com ou sem Gráficos de Segundo Plano) */
    .printable-receipt,
    .printable-receipt * {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    .printable-receipt h4,
    .printable-receipt h5,
    .printable-receipt h6,
    .printable-receipt p,
    .printable-receipt span,
    .printable-receipt td,
    .printable-receipt th,
    .printable-receipt strong {
      color: #000000 !important;
      -webkit-text-fill-color: #000000 !important;
    }

    .printable-receipt .text-muted,
    .printable-receipt label {
      color: #222222 !important;
      -webkit-text-fill-color: #222222 !important;
      font-weight: 600 !important;
    }

    /* Badges com contorno preto e texto preto (evita texto branco invisível) */
    .printable-receipt .badge {
      color: #000000 !important;
      -webkit-text-fill-color: #000000 !important;
      background-color: transparent !important;
      background: transparent !important;
      border: 1px solid #000000 !important;
      font-weight: 700 !important;
      padding: 2px 6px !important;
    }

    /* Bordas físicas para divisores e observações */
    .printable-receipt .border-bottom {
      border-bottom: 2px solid #000000 !important;
    }

    .printable-receipt .border-top {
      border-top: 1px solid #000000 !important;
    }

    .printable-receipt .bg-light {
      background-color: transparent !important;
      background: transparent !important;
      border: 1px solid #000000 !important;
    }

    /* Assinaturas lado a lado */
    .d-print-block {
      display: block !important;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }
  }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card shadow-sm border-0 printable-receipt">
      <div class="card-body p-4">
        <!-- Cabeçalho Oficial CCB -->
        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
          <div class="d-flex align-items-center">
            <img src="{{ asset('images/CCB_Logo_fundo_claro.png') }}" alt="CCB Logo" style="height: 65px;" class="me-3">
            <div>
              <h4 class="fw-bold mb-0 text-navy">{{ \App\Models\Setting::get('institution_name', 'CONGREGAÇÃO CRISTÃ NO BRASIL') }}</h4>
              <p class="text-muted mb-0 small">{{ \App\Models\Setting::get('administration_name', 'Gestão de Almoxarifado - Administração Nova Odessa') }}</p>
            </div>
          </div>
          <div class="text-end">
            <h5 class="fw-bold text-primary mb-1">{{ $movement->code }}</h5>
            <span class="badge {{ $movement->type->badgeClass() }} fs-6">{{ $movement->type->label() }}</span>
          </div>
        </div>

        <!-- Detalhes Gerais -->
        <div class="row g-3 mb-4">
          <div class="col-md-3 col-6">
            <label class="text-muted small d-block">Data / Hora</label>
            <span class="fw-semibold">{{ $movement->created_at->format('d/m/Y H:i:s') }}</span>
          </div>
          <div class="col-md-3 col-6">
            <label class="text-muted small d-block">Status</label>
            <span class="badge bg-secondary">{{ $movement->status->label() }}</span>
          </div>
          <div class="col-md-3 col-6">
            <label class="text-muted small d-block">Operador (Almoxarife)</label>
            <span class="fw-semibold">{{ $movement->user->name }}</span>
          </div>

          @if($movement->type === \App\Enums\MovementType::ENTRY)
          <div class="col-md-3 col-6">
            <label class="text-muted small d-block">Tipo de Documento</label>
            <span class="fw-semibold">{{ $movement->entryDocument?->document_type?->label() }}</span>
          </div>
          <div class="col-md-4 col-6">
            <label class="text-muted small d-block">Nº Documento / NF</label>
            <span class="fw-bold text-navy">{{ $movement->entryDocument?->document_number }}</span>
          </div>
          <div class="col-md-5 col-6">
            <label class="text-muted small d-block">Fornecedor / Doador</label>
            <span class="fw-semibold">{{ $movement->entryDocument?->supplier_or_donor }}</span>
          </div>
          <div class="col-md-3 col-6">
            <label class="text-muted small d-block">Valor Total</label>
            <span class="fw-bold text-success">
              @if($movement->entryDocument?->total_amount)
                R$ {{ number_format((float)$movement->entryDocument->total_amount, 2, ',', '.') }}
              @else
                -
              @endif
            </span>
          </div>
          @if($movement->type === \App\Enums\MovementType::ENTRY && $movement->entryDocument?->attachment)
          <div class="col-12 mt-2">
            <label class="text-muted small d-block mb-1"><i class="bi bi-paperclip text-primary me-1"></i>Comprovante / Anexos Digitais Vinculados</label>
            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('attachments.download', $movement->entryDocument->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> Baixar {{ $movement->entryDocument->attachment->original_name }} ({{ $movement->entryDocument->attachment->formattedSize() }})
              </a>
            </div>
          </div>
          @endif
          @else
          <div class="col-md-3 col-6">
            <label class="text-muted small d-block">Beneficiário (Quem retira)</label>
            <span class="fw-semibold">{{ $movement->beneficiary->name }}</span>
            <small class="text-muted d-block">({{ $movement->beneficiary->role_in_ccb ?? 'Voluntário' }})</small>
          </div>
          <div class="col-md-4 col-6">
            <label class="text-muted small d-block">Destino de Aplicação</label>
            <span class="fw-semibold">{{ $movement->destination->code }} - {{ $movement->destination->name }}</span>
          </div>
          @endif

          @if($movement->notes)
          <div class="col-12">
            <label class="text-muted small d-block">Observações</label>
            <div class="p-2 bg-light rounded border text-muted small">{{ $movement->notes }}</div>
          </div>
          @endif
        </div>

        <!-- Tabela de Itens -->
        <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Itens da Movimentação</h6>
        <div class="table-responsive mb-4">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>SKU</th>
                <th>Material</th>
                <th>Qtd. Solicitada</th>
                @if($movement->type === \App\Enums\MovementType::LOAN)
                <th>Qtd. Devolvida</th>
                <th>Prev. Retorno</th>
                <th>Status Item</th>
                <th class="text-center no-print">Ações Devolução</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($movement->items as $item)
              <tr>
                <td class="fw-bold text-navy">{{ $item->material->code_sku }}</td>
                <td>
                  {{ $item->material->name }}
                  @if($item->material->ca_number)
                    <small class="d-block text-muted">CA: {{ $item->material->ca_number }}</small>
                  @endif
                </td>
                <td class="fw-bold fs-6">{{ $item->quantity }} {{ $item->material->unit_measure }}</td>
                @if($movement->type === \App\Enums\MovementType::LOAN)
                <td>{{ $item->returned_quantity }} {{ $item->material->unit_measure }}</td>
                <td>
                  @if($item->expected_return_date)
                    <span class="{{ $item->isOverdue() ? 'text-danger fw-bold' : '' }}">
                      {{ $item->expected_return_date->format('d/m/Y') }}
                      @if($item->isOverdue()) <i class="bi bi-exclamation-triangle"></i> @endif
                    </span>
                  @else
                    -
                  @endif
                </td>
                <td>
                  @if($item->status === \App\Enums\ItemStatus::RETURNED)
                    <span class="badge bg-success">Devolvido</span>
                  @elseif($item->status === \App\Enums\ItemStatus::PENDING_RETURN)
                    <span class="badge bg-warning text-dark">Pendente</span>
                  @else
                    <span class="badge bg-secondary">Entregue</span>
                  @endif
                </td>
                <td class="text-center no-print">
                  @if($item->pendingQuantity() > 0)
                    @can('create-movements')
                    <button type="button" 
                            class="btn btn-outline-success btn-sm btn-return-modal" 
                            data-item-id="{{ $item->id }}"
                            data-material-name="{{ $item->material->name }}"
                            data-pending="{{ $item->pendingQuantity() }}"
                            data-unit="{{ $item->material->unit_measure }}">
                      <i class="bi bi-arrow-counterclockwise me-1"></i> Devolver
                    </button>
                    @endcan
                  @else
                    <span class="text-muted small"><i class="bi bi-check-all text-success"></i> Concluído</span>
                  @endif
                </td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Assinaturas (Exibidas apenas na Impressão/PDF) -->
        <div class="d-none d-print-block mt-5 pt-4">
          <div class="row text-center">
            <div class="col-6">
              <div class="border-top border-dark pt-2 mx-4">
                <strong>{{ $movement->user->name }}</strong><br>
                <small>Almoxarife Responsável</small>
              </div>
            </div>
            <div class="col-6">
              <div class="border-top border-dark pt-2 mx-4">
                <strong>{{ $movement->type === \App\Enums\MovementType::ENTRY ? ($movement->entryDocument?->supplier_or_donor ?? 'Fornecedor/Doador') : $movement->beneficiary->name }}</strong><br>
                <small>{{ $movement->type === \App\Enums\MovementType::ENTRY ? 'Entregue por / Fornecedor' : 'Retirado por / Beneficiário' }}</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Botões de Impressão e Exportação em PDF -->
        <div class="d-flex justify-content-between align-items-center pt-3 border-top no-print">
          <a href="{{ route('movements.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i> Voltar
          </a>
          <div class="btn-group">
            <a href="{{ route('movements.pdf', $movement) }}" class="btn btn-outline-danger" target="_blank">
              <i class="bi bi-file-earmark-pdf me-1"></i> Baixar PDF
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()">
              <i class="bi bi-printer me-1"></i> Imprimir Comprovante
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Devolução Parcial/Total -->
<div class="modal fade" id="modalReturnItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formReturnItem" method="POST" action="">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold"><i class="bi bi-arrow-counterclockwise text-success me-2"></i>Devolver Ferramenta / Equipamento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-2">Material: <strong id="returnMaterialName">-</strong></p>
          <p class="mb-3 text-muted small">Quantidade pendente de devolução: <span id="returnPendingQty" class="badge bg-warning text-dark">-</span></p>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Quantidade a Devolver *</label>
            <input type="number" name="quantity" id="inputReturnQty" class="form-control" value="1" min="1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Confirmar Devolução</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalReturnItem = new bootstrap.Modal(document.getElementById('modalReturnItem'));
    const formReturnItem = document.getElementById('formReturnItem');
    const returnMaterialName = document.getElementById('returnMaterialName');
    const returnPendingQty = document.getElementById('returnPendingQty');
    const inputReturnQty = document.getElementById('inputReturnQty');

    document.querySelectorAll('.btn-return-modal').forEach(btn => {
      btn.addEventListener('click', function() {
        const itemId = btn.dataset.itemId;
        const name = btn.dataset.materialName;
        const pending = btn.dataset.pending;
        const unit = btn.dataset.unit;

        formReturnItem.action = `/movements/items/${itemId}/return`;
        returnMaterialName.textContent = name;
        returnPendingQty.textContent = `${pending} ${unit}`;
        inputReturnQty.max = pending;
        inputReturnQty.value = pending;

        modalReturnItem.show();
      });
    });
  });
</script>
@endpush
