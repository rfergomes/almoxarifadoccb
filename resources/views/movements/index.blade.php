@extends('layouts.app')

@section('title', 'Movimentações | Almoxarifado CCB')
@section('page_title', 'Histórico de Saídas & Empréstimos')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-arrow-left-right text-success me-2"></i>Movimentações de Estoque</h5>
    @can('create-movements')
    <a href="{{ route('movements.create') }}" class="btn btn-success btn-sm rounded-pill ms-auto">
      <i class="bi bi-plus-circle me-1"></i> Nova Saída / Empréstimo
    </a>
    @endcan
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Código</th>
            <th>Data / Hora</th>
            <th>Tipo</th>
            <th>Beneficiário (Retirou)</th>
            <th>Destino (Aplicação)</th>
            <th>Qtd. Itens</th>
            <th>Status</th>
            <th class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($movements as $mov)
          <tr>
            <td class="fw-bold text-navy">{{ $mov->code }}</td>
            <td><small>{{ $mov->created_at->format('d/m/Y H:i') }}</small></td>
            <td>
              <span class="badge {{ $mov->type->badgeClass() }}">{{ $mov->type->label() }}</span>
            </td>
            <td>
              <div class="fw-bold">{{ $mov->beneficiary?->name }}</div>
              <small class="text-muted">{{ $mov->beneficiary?->role_in_ccb ?? 'Voluntário' }}</small>
            </td>
            <td>{{ $mov->destination?->name }}</td>
            <td><span class="badge bg-light text-dark border">{{ $mov->items->count() }} item(ns)</span></td>
            <td>
              <span class="badge {{ $mov->status->badgeClass() }}">{{ $mov->status->label() }}</span>
            </td>
            <td class="text-center">
              <a href="{{ route('movements.show', $mov) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Visualizar Detalhes">
                <i class="bi bi-eye"></i> Detalhes
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">Nenhuma movimentação registrada até o momento.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($movements->hasPages())
  <div class="card-footer bg-white border-0">
    {{ $movements->links() }}
  </div>
  @endif
</div>
@endsection
