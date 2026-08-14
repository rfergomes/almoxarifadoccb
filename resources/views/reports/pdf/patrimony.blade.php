@extends('reports.pdf.template')

@section('report_title', 'Relatório de Bens Patrimoniados')
@section('report_name', 'Registro de Patrimônio de Equipamentos e Ferramentas')

@section('content')
<table class="data-table">
  <thead>
    <tr>
      <th>Código de Patrimônio</th>
      <th>SKU</th>
      <th>Nome do Equipamento / Ferramenta</th>
      <th>Categoria</th>
      <th>Estoque Armazenado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($materials as $mat)
    <tr>
      <td><strong>{{ $mat->patrimony_code }}</strong></td>
      <td>{{ $mat->code_sku }}</td>
      <td>{{ $mat->name }}</td>
      <td>{{ $mat->category?->name ?? 'Geral' }}</td>
      <td><strong>{{ $mat->current_stock }} {{ $mat->unit_measure }}</strong></td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
