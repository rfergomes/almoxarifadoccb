@extends('layouts.app')

@section('title', 'Configurações do Sistema | Almoxarifado CCB')
@section('page_title', 'Customização de Títulos e Parâmetros Institucionais')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-sliders text-primary me-2"></i>Personalizar Títulos do Sistema e Relatórios</h5>
      </div>
      <div class="card-body p-4">
        <form action="{{ route('settings.update') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-bold">Nome da Instituição Principal *</label>
            <input type="text" name="institution_name" class="form-control" value="{{ old('institution_name', $settings['institution_name']) }}" required>
            <small class="text-muted">Exibido no cabeçalho das páginas, rodapé, comprovantes e relatórios em PDF.</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Nome da Administração / Unidade *</label>
            <input type="text" name="administration_name" class="form-control" value="{{ old('administration_name', $settings['administration_name']) }}" required>
            <small class="text-muted">Exibido abaixo do nome da instituição em todos os documentos oficiais.</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Título dos Comprovantes de Movimentação *</label>
            <input type="text" name="receipt_header_title" class="form-control" value="{{ old('receipt_header_title', $settings['receipt_header_title']) }}" required>
            <small class="text-muted">Título impresso nas vias de saída e entrada de estoque.</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Título dos Relatórios Gerenciais PDF *</label>
            <input type="text" name="reports_header_title" class="form-control" value="{{ old('reports_header_title', $settings['reports_header_title']) }}" required>
            <small class="text-muted">Cabeçalho de relatórios exportados na Central de Relatórios.</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Título do Termo de Inventário Geral *</label>
            <input type="text" name="inventory_header_title" class="form-control" value="{{ old('inventory_header_title', $settings['inventory_header_title']) }}" required>
            <small class="text-muted">Título impresso na folha oficial de inventário físico e acerto de estoque.</small>
          </div>

          <div class="mb-4">
            <label class="form-label fw-bold">E-mail do Suporte / Desenvolvedor *</label>
            <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $settings['support_email']) }}" required>
            <small class="text-muted">Exibido no rodapé da aplicação para contato direto.</small>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
              <i class="bi bi-check-circle me-1"></i> Salvar Alterações
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
