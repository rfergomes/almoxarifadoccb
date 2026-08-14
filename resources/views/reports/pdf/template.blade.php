<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>@yield('report_title', 'Relatório - Almoxarifado CCB')</title>
  <style>
    @page {
      margin: 1.5cm 1.5cm 2cm 1.5cm;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11px;
      color: #333;
      line-height: 1.4;
    }
    .header {
      width: 100%;
      border-bottom: 2px solid #1a252f;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .header table {
      width: 100%;
    }
    .logo {
      max-height: 55px;
      width: auto;
    }
    .title-area {
      text-align: right;
    }
    .title-area h2 {
      margin: 0;
      font-size: 16px;
      color: #1a252f;
      text-transform: uppercase;
    }
    .title-area p {
      margin: 3px 0 0 0;
      font-size: 10px;
      color: #666;
    }
    .footer {
      position: fixed;
      bottom: -1cm;
      left: 0;
      right: 0;
      height: 1cm;
      border-top: 1px solid #ddd;
      padding-top: 5px;
      font-size: 9px;
      color: #777;
    }
    .footer table {
      width: 100%;
    }
    .page-number:after {
      content: counter(page);
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .data-table th {
      background-color: #f1f5f9;
      color: #1a252f;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 9px;
      padding: 6px 8px;
      border: 1px solid #cbd5e1;
      text-align: left;
    }
    .data-table td {
      padding: 5px 8px;
      border: 1px solid #e2e8f0;
      font-size: 10px;
    }
    .data-table tr:nth-child(even) {
      background-color: #f8fafc;
    }
    .badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 3px;
      font-size: 9px;
      font-weight: bold;
    }
    .badge-success { background-color: #d1fae5; color: #065f46; }
    .badge-warning { background-color: #fef3c7; color: #92400e; }
    .badge-danger { background-color: #fee2e2; color: #991b1b; }
    .badge-info { background-color: #e0f2fe; color: #075985; }
  </style>
</head>
<body>
  <div class="header">
    <table>
      <tr>
        <td style="width: 40%;">
          @if(extension_loaded('gd') && file_exists(public_path('images/CCB_Logo_preto_fundo_branco.png')))
            <img src="{{ public_path('images/CCB_Logo_preto_fundo_branco.png') }}" class="logo" alt="CCB Logo">
          @else
            <strong style="font-size: 16px; color: #1a252f;">CCB ALMOXARIFADO</strong>
          @endif
        </td>
        <td class="title-area" style="width: 60%;">
          <h2>{{ \App\Models\Setting::get('institution_name', 'CONGREGAÇÃO CRISTÃ NO BRASIL') }}</h2>
          <p>{{ \App\Models\Setting::get('administration_name', 'Gestão de Almoxarifado - Administração Nova Odessa') }}</p>
          <p><strong>@yield('report_name', \App\Models\Setting::get('reports_header_title', 'Relatório Gerencial'))</strong></p>
        </td>
      </tr>
    </table>
  </div>

  @yield('content')

  <div class="footer">
    <table>
      <tr>
        <td style="width: 50%;">
          Gerado em: {{ date('d/m/Y H:i:s') }} &bull; {{ \App\Models\Setting::get('institution_name', 'CCB Almoxarifado') }}
        </td>
        <td style="width: 50%; text-align: right;">
          Página <span class="page-number"></span>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
