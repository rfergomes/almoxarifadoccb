@extends('reports.pdf.template')

@section('report_title', 'Relatório de Empréstimos em Atraso')
@section('report_name', 'Empréstimos com Devolução em Atraso')

@section('content')
<table class="data-table">
  <thead>
    <tr>
      <th>Código Mov.</th>
      <th>Material</th>
      <th>Beneficiário (Retirou)</th>
      <th>Destino</th>
      <th>Qtd. Pendente</th>
      <th>Prev. Retorno</th>
    </tr>
  </thead>
  <tbody>
    @forelse($items as $item)
    <tr>
      <td><strong>{{ $item->movement?->code }}</strong></td>
      <td>{{ $item->material?->name }}</td>
      <td>{{ $item->movement?->beneficiary?->name }}</td>
      <td>{{ $item->movement?->destination?->name }}</td>
      <td><span class="badge bg-danger">{{ $item->pendingQuantity() }} {{ $item->material?->unit_measure }}</span></td>
      <td style="color: #ef4444; font-weight: bold;">{{ $item->expected_return_date?->format('d/m/Y') }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="6" style="text-align: center; padding: 15px;">Nenhum empréstimo em atraso registrado.</td>
    </tr>
    @endforelse
  </tbody>
</table>
@endsection
