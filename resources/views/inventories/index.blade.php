@extends('layouts.app')

@section('title', 'Inventário Geral | Almoxarifado CCB')
@section('page_title', 'Inventário Geral Periódico de Estoque')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-clipboard-check text-success me-2"></i>Sessões de Inventário Geral</h5>
    @can('manage-materials')
    <a href="{{ route('inventories.create') }}" class="btn btn-success btn-sm rounded-pill ms-auto">
      <i class="bi bi-plus-circle me-1"></i> Iniciar Novo Inventário Geral
    </a>
    @endcan
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Código</th>
            <th>Título do Inventário</th>
            <th>Responsável (Quem realizou)</th>
            <th>Data/Hora Abertura</th>
            <th>Total Itens Auditados</th>
            <th>Divergências Ajustadas</th>
            <th>Status</th>
            <th class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inventories as $inv)
          <tr>
            <td class="fw-bold text-navy">{{ $inv->code }}</td>
            <td class="fw-semibold">{{ $inv->title }}</td>
            <td>
              <i class="bi bi-person-circle text-primary me-1"></i>{{ $inv->user->name }}
            </td>
            <td><small>{{ $inv->started_at->format('d/m/Y H:i') }}</small></td>
            <td><span class="badge bg-light text-dark border">{{ $inv->total_items }} materiais</span></td>
            <td>
              @if($inv->isCompleted())
                <span class="badge bg-warning text-dark">{{ $inv->items_adjusted }} ajustado(s)</span>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              @if($inv->isOpen())
                <span class="badge bg-primary">Em Andamento</span>
              @elseif($inv->isCompleted())
                <span class="badge bg-success">Concluído</span>
              @else
                <span class="badge bg-secondary">{{ $inv->status }}</span>
              @endif
            </td>
            <td class="text-center">
              <div class="btn-group">
                <a href="{{ route('inventories.show', $inv) }}" class="btn btn-outline-primary btn-sm rounded-start-pill" title="Preencher / Visualizar Contagem">
                  <i class="bi bi-pencil-square"></i> {{ $inv->isOpen() ? 'Preencher Contagem' : 'Ver Inventário' }}
                </a>
                <a href="{{ route('inventories.pdf', $inv) }}" class="btn btn-outline-danger btn-sm rounded-end-pill" target="_blank" title="Baixar Termo em PDF">
                  <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">Nenhum Inventário Geral realizado até o momento.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($inventories->hasPages())
  <div class="card-footer bg-white border-0">
    {{ $inventories->links() }}
  </div>
  @endif
</div>
@endsection
