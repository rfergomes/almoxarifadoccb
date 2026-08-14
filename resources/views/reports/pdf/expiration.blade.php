@extends('reports.pdf.template')

@section('report_title', 'Relatório de Validade de Insumos')
@section('report_name', 'Controle e Alertas de Validade de Produtos')

@section('content')
<table class="data-table">
  <thead>
    <tr>
      <th>SKU</th>
      <th>Nome do Material / Insumo</th>
      <th>Categoria</th>
      <th>Estoque Atual</th>
      <th>Data de Validade</th>
      <th>Status de Validade</th>
    </tr>
  </thead>
  <tbody>
    @foreach($materials as $mat)
    <tr>
      <td><strong>{{ $mat->code_sku }}</strong></td>
      <td>{{ $mat->name }}</td>
      <td>{{ $mat->category?->name ?? 'Geral' }}</td>
      <td><strong>{{ $mat->current_stock }} {{ $mat->unit_measure }}</strong></td>
      <td>{{ $mat->expiration_date?->format('d/m/Y') ?? 'Indefinida' }}</td>
      <td>
        <span class="{{ $mat->expirationStatus()->badgeClass() }}">
          {{ $mat->expirationStatus()->label() }}
        </span>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
