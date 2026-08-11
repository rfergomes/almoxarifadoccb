@extends('reports.pdf.template')

@section('report_title', 'Termo de Inventário Geral - ' . $inventory->code)
@section('report_name', 'Termo Oficial de Inventário Geral Periódico')

@section('content')
<div style="margin-bottom: 12px; font-size: 10px;">
  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <td style="width: 50%;"><strong>Código do Inventário:</strong> {{ $inventory->code }}</td>
      <td style="width: 50%; text-align: right;"><strong>Status:</strong> {{ $inventory->isCompleted() ? 'CONCLUÍDO' : 'EM ANDAMENTO' }}</td>
    </tr>
    <tr>
      <td><strong>Título:</strong> {{ $inventory->title }}</td>
      <td style="text-align: right;"><strong>Abertura:</strong> {{ $inventory->started_at->format('d/m/Y H:i') }}</td>
    </tr>
    <tr>
      <td><strong>Auditor Responsável:</strong> {{ $inventory->user->name }}</td>
      <td style="text-align: right;"><strong>Conclusão:</strong> {{ $inventory->completed_at?->format('d/m/Y H:i') ?? 'Em Aberto' }}</td>
    </tr>
  </table>
</div>

<table class="data-table">
  <thead>
    <tr>
      <th>SKU</th>
      <th>Nome do Material</th>
      <th>Categoria</th>
      <th style="text-align: center;">Saldo Sistema</th>
      <th style="text-align: center;">Contagem Física</th>
      <th style="text-align: center;">Divergência</th>
      <th>Observação</th>
    </tr>
  </thead>
  <tbody>
    @foreach($inventory->items as $item)
    <tr>
      <td><strong>{{ $item->material->code_sku }}</strong></td>
      <td>{{ $item->material->name }}</td>
      <td>{{ $item->material->category?->name ?? 'Geral' }}</td>
      <td style="text-align: center;">{{ $item->system_stock }} {{ $item->material->unit_measure }}</td>
      <td style="text-align: center;"><strong>{{ $item->counted_stock ?? '-' }} {{ $item->material->unit_measure }}</strong></td>
      <td style="text-align: center;">
        @if($item->difference !== null)
          @if($item->difference > 0)
            <span class="badge bg-success">+{{ $item->difference }} (Sobra)</span>
          @elseif($item->difference < 0)
            <span class="badge bg-danger">{{ $item->difference }} (Falta)</span>
          @else
            <span class="badge bg-info">0 (OK)</span>
          @endif
        @else
          -
        @endif
      </td>
      <td><small>{{ $item->notes ?? '-' }}</small></td>
    </tr>
    @endforeach
  </tbody>
</table>

<div style="margin-top: 50px; width: 100%;">
  <table style="width: 100%; border-collapse: collapse; text-align: center;">
    <tr>
      <td style="width: 50%;">
        <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
          <strong>{{ $inventory->user->name }}</strong><br>
          <small>Auditor Responsável pelo Inventário</small>
        </div>
      </td>
      <td style="width: 50%;">
        <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
          <strong>Administração do Almoxarifado CCB</strong><br>
          <small>Visto de Conferência & Homologação</small>
        </div>
      </td>
    </tr>
  </table>
</div>
@endsection
