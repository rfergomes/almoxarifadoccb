@extends('layouts.app')

@section('title', 'Entradas | Almoxarifado CCB')
@section('page_title', 'Histórico de Entradas de Estoque (NF / Doações)')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-box-arrow-in-down text-info me-2"></i>Documentos de Entrada Registrados</h5>
    @can('create-movements')
    <a href="{{ route('entries.create') }}" class="btn btn-info text-white btn-sm rounded-pill ms-auto">
      <i class="bi bi-plus-circle me-1"></i> Nova Entrada (NF / Doação)
    </a>
    @endcan
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Código Entrada</th>
            <th>Data Lançamento</th>
            <th>Tipo Documento</th>
            <th>Nº Documento / NF</th>
            <th>Fornecedor / Doador</th>
            <th>Qtd. Itens</th>
            <th>Valor Total</th>
            <th class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($entries as $ent)
          <tr>
            <td class="fw-bold text-navy">{{ $ent->code }}</td>
            <td><small>{{ $ent->created_at->format('d/m/Y H:i') }}</small></td>
            <td>
              <span class="badge bg-secondary">{{ $ent->entryDocument?->document_type?->label() ?? 'Documento' }}</span>
            </td>
            <td class="fw-bold">
              {{ $ent->entryDocument?->document_number }}
              @if($ent->entryDocument?->attachment)
                <a href="{{ route('attachments.download', $ent->entryDocument->attachment) }}" target="_blank" class="badge bg-primary text-decoration-none ms-1" title="Baixar/Visualizar Comprovante: {{ $ent->entryDocument->attachment->original_name }}">
                  <i class="bi bi-paperclip me-1"></i>Anexo
                </a>
              @endif
            </td>
            <td>{{ $ent->entryDocument?->supplier_or_donor }}</td>
            <td><span class="badge bg-light text-dark border">{{ $ent->items->count() }} item(ns)</span></td>
            <td class="fw-bold text-success">
              @if($ent->entryDocument?->total_amount)
                R$ {{ number_format((float)$ent->entryDocument->total_amount, 2, ',', '.') }}
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td class="text-center">
              <a href="{{ route('movements.show', $ent) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="bi bi-eye"></i> Detalhes
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">Nenhuma entrada de estoque registrada.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($entries->hasPages())
  <div class="card-footer bg-white border-0">
    {{ $entries->links() }}
  </div>
  @endif
</div>
@endsection
