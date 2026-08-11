@extends('reports.pdf.template')

@section('report_title', 'Relatório de Movimentações')
@section('report_name', 'Histórico Geral de Entradas, Saídas e Empréstimos')

@section('content')
<table class="data-table">
  <thead>
    <tr>
      <th>Código</th>
      <th>Data</th>
      <th>Tipo</th>
      <th>Beneficiário / Documento</th>
      <th>Destino / Fornecedor</th>
      <th>Qtd. Itens</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @foreach($movements as $mov)
    <tr>
      <td><strong>{{ $mov->code }}</strong></td>
      <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
      <td><span class="badge bg-info">{{ $mov->type->label() }}</span></td>
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
      <td>{{ $mov->items->count() }} item(ns)</td>
      <td><span class="badge bg-secondary">{{ $mov->status->label() }}</span></td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
