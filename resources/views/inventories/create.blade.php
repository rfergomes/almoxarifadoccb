@extends('layouts.app')

@section('title', 'Iniciar Inventário Geral | Almoxarifado CCB')
@section('page_title', 'Abrir Nova Sessão de Inventário Geral Periódico')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-clipboard-plus text-success me-2"></i>Abertura de Inventário Geral</h5>
      </div>
      <form action="{{ route('inventories.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="alert alert-info py-2 small mb-4">
            <i class="bi bi-info-circle me-1"></i> Ao iniciar o Inventário Geral, o sistema registrará os saldos atuais de todos os materiais ativos no almoxarifado para contagem e conferência física.
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Título do Inventário *</label>
            <input type="text" name="title" class="form-control" placeholder="Ex: Inventário Geral do 3º Trimestre / 2026" value="Inventário Geral {{ date('m/Y') }}" required autofocus>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Responsável da Auditoria (Quem realizará a contagem)</label>
            <input type="text" class="form-control bg-light" value="{{ Auth::user()->name }} ({{ Auth::user()->email }})" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Observações / Objetivo do Inventário</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Ex: Contagem física completa prévia para encerramento de balanço anual."></textarea>
          </div>
        </div>

        <div class="card-footer bg-white py-3 text-end border-top">
          <a href="{{ route('inventories.index') }}" class="btn btn-light me-2">Cancelar</a>
          <button type="submit" class="btn btn-success px-4 fw-bold">
            <i class="bi bi-check-circle me-1"></i> Criar e Iniciar Contagem Física
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
