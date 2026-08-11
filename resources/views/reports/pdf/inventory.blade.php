@extends('reports.pdf.template')

@section('report_title', 'Relatório de Posição de Estoque')
@section('report_name', 'Posição Geral de Estoque de Materiais')

@section('content')
<table class="data-table">
  <thead>
    <tr>
      <th>SKU</th>
      <th>Nome do Material</th>
      <th>Categoria</th>
      <th>Unidade</th>
      <th>Estoque Atual</th>
      <th>Estoque Mínimo</th>
      <th>CA (EPI)</th>
      <th>Status Saldo</th>
    </tr>
  </thead>
  <tbody>
    @foreach($materials as $mat)
    <tr>
      <td><strong>{{ $mat->code_sku }}</strong></td>
      <td>{{ $mat->name }}</td>
      <td>{{ $mat->category?->name ?? 'Geral' }}</td>
      <td>{{ $mat->unit_measure }}</td>
      <td><strong>{{ $mat->current_stock }}</strong></td>
      <td>{{ $mat->minimum_stock }}</td>
      <td>{{ $mat->ca_number ?? '-' }}</td>
      <td>
        @if($mat->isStockLow())
          <span class="badge bg-danger">ESTOQUE BAIXO</span>
        @else
          <span class="badge bg-success">NORMAL</span>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
