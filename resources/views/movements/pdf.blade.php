@extends('reports.pdf.template')

@section('report_title', 'Comprovante de Movimentação - ' . $movement->code)
@section('report_name', 'Comprovante Oficial de Movimentação de Estoque')

@section('content')
<div style="margin-bottom: 15px; font-size: 11px;">
  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <td style="width: 50%;"><strong>Código da Movimentação:</strong> {{ $movement->code }}</td>
      <td style="width: 50%; text-align: right;"><strong>Tipo:</strong> {{ $movement->type->label() }}</td>
    </tr>
    <tr>
      <td><strong>Data / Hora:</strong> {{ $movement->created_at->format('d/m/Y H:i:s') }}</td>
      <td style="text-align: right;"><strong>Status:</strong> {{ $movement->status->label() }}</td>
    </tr>
    <tr>
      <td><strong>Operador (Almoxarife):</strong> {{ $movement->user->name }}</td>
      <td style="text-align: right;">
        @if($movement->type === \App\Enums\MovementType::ENTRY)
          <strong>Doc/NF:</strong> {{ $movement->entryDocument?->document_number }}
        @else
          <strong>Beneficiário:</strong> {{ $movement->beneficiary->name }}
        @endif
      </td>
    </tr>
    @if($movement->type === \App\Enums\MovementType::ENTRY)
    <tr>
      <td><strong>Fornecedor / Doador:</strong> {{ $movement->entryDocument?->supplier_or_donor }}</td>
      <td style="text-align: right;">
        <strong>Valor Total:</strong> 
        @if($movement->entryDocument?->total_amount)
          R$ {{ number_format((float)$movement->entryDocument->total_amount, 2, ',', '.') }}
        @else
          -
        @endif
      </td>
    </tr>
    @else
    <tr>
      <td><strong>Destino de Aplicação:</strong> {{ $movement->destination->code }} - {{ $movement->destination->name }}</td>
      <td style="text-align: right;"><strong>Função:</strong> {{ $movement->beneficiary->role_in_ccb ?? 'Voluntário' }}</td>
    </tr>
    @endif
    @if($movement->notes)
    <tr>
      <td colspan="2" style="padding-top: 5px;"><strong>Observações:</strong> {{ $movement->notes }}</td>
    </tr>
    @endif
  </table>
</div>

<table class="data-table">
  <thead>
    <tr>
      <th>SKU</th>
      <th>Nome do Material</th>
      <th>Categoria</th>
      <th style="text-align: center;">Qtd. Solicitada</th>
      @if($movement->type === \App\Enums\MovementType::LOAN)
      <th style="text-align: center;">Qtd. Devolvida</th>
      <th style="text-align: center;">Prev. Retorno</th>
      <th style="text-align: center;">Status Item</th>
      @endif
    </tr>
  </thead>
  <tbody>
    @foreach($movement->items as $item)
    <tr>
      <td><strong>{{ $item->material->code_sku }}</strong></td>
      <td>
        {{ $item->material->name }}
        @if($item->material->ca_number)
          <br><small style="color: #666;">CA: {{ $item->material->ca_number }}</small>
        @endif
      </td>
      <td>{{ $item->material->category?->name ?? 'Geral' }}</td>
      <td style="text-align: center;"><strong>{{ $item->quantity }} {{ $item->material->unit_measure }}</strong></td>
      @if($movement->type === \App\Enums\MovementType::LOAN)
      <td style="text-align: center;">{{ $item->returned_quantity }} {{ $item->material->unit_measure }}</td>
      <td style="text-align: center;">{{ $item->expected_return_date?->format('d/m/Y') ?? '-' }}</td>
      <td style="text-align: center;">{{ $item->status->label() }}</td>
      @endif
    </tr>
    @endforeach
  </tbody>
</table>

<div style="margin-top: 60px; width: 100%;">
  <table style="width: 100%; border-collapse: collapse; text-align: center;">
    <tr>
      <td style="width: 50%;">
        <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
          <strong>{{ $movement->user->name }}</strong><br>
          <small>Almoxarife Responsável</small>
        </div>
      </td>
      <td style="width: 50%;">
        <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
          <strong>{{ $movement->type === \App\Enums\MovementType::ENTRY ? ($movement->entryDocument?->supplier_or_donor ?? 'Fornecedor/Doador') : $movement->beneficiary->name }}</strong><br>
          <small>{{ $movement->type === \App\Enums\MovementType::ENTRY ? 'Entregue por / Fornecedor' : 'Retirado por / Beneficiário' }}</small>
        </div>
      </td>
    </tr>
  </table>
</div>
@endsection
