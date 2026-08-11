@extends('layouts.app')

@section('title', 'Nova Entrada | Almoxarifado CCB')
@section('page_title', 'Lançar Nova Entrada de Estoque (NF / Doação)')

@section('content')
<form action="{{ route('entries.store') }}" method="POST" id="formEntry">
  @csrf
  <div class="row g-3">
    <!-- Dados do Documento -->
    <div class="col-lg-12">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="bi bi-file-earmark-text text-info me-2"></i>Dados do Documento de Origem</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label fw-semibold">Tipo de Documento *</label>
              <select name="document_type" class="form-select" required>
                <option value="NOTA_FISCAL">Nota Fiscal de Compra</option>
                <option value="DOACAO">Termo de Doação</option>
                <option value="COMPRA_DIRETA">Compra Direta / Recibo</option>
                <option value="OUTRO">Outro Documento</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Nº do Documento / NF *</label>
              <input type="text" name="document_number" class="form-control" placeholder="Ex: NF-10025 ou DOACAO-2026/01" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fornecedor ou Doador *</label>
              <input type="text" name="supplier_or_donor" class="form-control" placeholder="Ex: Votorantim Cimentos S.A." required>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Data Emissão</label>
              <input type="date" name="issued_at" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Valor Total (R$)</label>
              <input type="number" step="0.01" name="total_amount" class="form-control" placeholder="0.00">
            </div>
            <div class="col-md-9">
              <label class="form-label fw-semibold">Observações / Detalhes</label>
              <input type="text" name="notes" class="form-control" placeholder="Ex: Material doado por irmão da C.O. Central para a reforma.">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabela Dinâmica de Itens da Entrada -->
    <div class="col-lg-12">
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="bi bi-box-seam text-warning me-2"></i>Materiais para Entrada no Estoque</h5>
          <div class="ms-auto d-flex gap-2">
            @can('manage-materials')
            <button type="button" class="btn btn-outline-warning btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalQuickMaterial">
              <i class="bi bi-plus-circle me-1"></i> Cadastrar Novo Material Não Cadastrado
            </button>
            @endcan
            <button type="button" class="btn btn-primary btn-sm rounded-pill" id="btnAddEntryItem">
              <i class="bi bi-plus-circle me-1"></i> Adicionar Outro Material
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 50%;">Material *</th>
                  <th style="width: 20%;">Estoque Atual</th>
                  <th style="width: 20%;">Qtd. Entrada *</th>
                  <th style="width: 10%;" class="text-center">Ações</th>
                </tr>
              </thead>
              <tbody id="entryItemsBody">
                <!-- Linhas inseridas via JS -->
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer bg-white border-0 py-3 text-end">
          <a href="{{ route('entries.index') }}" class="btn btn-light me-2">Cancelar</a>
          <button type="button" class="btn btn-success px-4" id="btnSubmitEntry">
            <i class="bi bi-check-circle me-1"></i> Confirmar Entrada de Estoque
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

@include('partials.modal_quick_material', ['categories' => $categories])

<!-- Template de Linha oculto -->
<template id="entryRowTemplate">
  <tr class="entry-item-row">
    <td>
      <select name="items[INDEX][material_id]" class="form-select select-material-entry" required>
        <option value="">Selecione o material...</option>
        @foreach($materials as $mat)
          <option value="{{ $mat->id }}" data-stock="{{ $mat->current_stock }}" data-unit="{{ $mat->unit_measure }}">
            {{ $mat->code_sku }} - {{ $mat->name }} (Atual: {{ $mat->current_stock }} {{ $mat->unit_measure }})
          </option>
        @endforeach
      </select>
    </td>
    <td>
      <span class="stock-badge badge bg-secondary fs-6">-</span>
    </td>
    <td>
      <input type="number" name="items[INDEX][quantity]" class="form-control" value="1" min="1" required>
    </td>
    <td class="text-center">
      <button type="button" class="btn btn-outline-danger btn-sm btn-remove-entry-row" title="Remover este item">
        <i class="bi bi-trash"></i>
      </button>
    </td>
  </tr>
</template>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const entryItemsBody = document.getElementById('entryItemsBody');
    const btnAddEntryItem = document.getElementById('btnAddEntryItem');
    const entryRowTemplate = document.getElementById('entryRowTemplate').content;
    let rowIndex = 0;

    function addEntryRow(selectedMaterialId = null, selectedStock = null, selectedUnit = null) {
      const clone = document.importNode(entryRowTemplate, true);
      const row = clone.querySelector('tr');
      row.innerHTML = row.innerHTML.replace(/INDEX/g, rowIndex++);
      entryItemsBody.appendChild(row);

      const selectMaterial = row.querySelector('.select-material-entry');
      const stockBadge = row.querySelector('.stock-badge');

      $(selectMaterial).select2({
        theme: 'bootstrap-5',
        placeholder: 'Selecione o material...',
        width: '100%'
      });

      $(selectMaterial).on('change', function() {
        const option = selectMaterial.options[selectMaterial.selectedIndex];
        if (option && option.value) {
          stockBadge.textContent = `${option.dataset.stock} ${option.dataset.unit}`;
          stockBadge.className = 'badge bg-info fs-6';
        } else {
          stockBadge.textContent = '-';
          stockBadge.className = 'badge bg-secondary fs-6';
        }
      });

      if (selectedMaterialId) {
        selectMaterial.value = selectedMaterialId;
        $(selectMaterial).trigger('change');
      }

      row.querySelector('.btn-remove-entry-row').addEventListener('click', function() {
        if (entryItemsBody.querySelectorAll('tr').length > 1) {
          $(selectMaterial).select2('destroy');
          row.remove();
        } else {
          toastr.warning('A entrada deve possuir ao menos um item!');
        }
      });

      return selectMaterial;
    }

    btnAddEntryItem.addEventListener('click', () => addEntryRow());
    addEntryRow();

    // AJAX Modal Material Rápido
    document.getElementById('btnSaveQuickMaterial').addEventListener('click', function() {
      const form = document.getElementById('formQuickMaterial');
      const formData = new FormData(form);

      fetch('/api/quick-material', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const mat = data.data;

          // Atualiza o template oculto
          const templateSelect = document.getElementById('entryRowTemplate').content.querySelector('.select-material-entry');
          const newOptTemplate = new Option(mat.label, mat.id);
          newOptTemplate.dataset.stock = mat.current_stock;
          newOptTemplate.dataset.unit = mat.unit_measure;
          templateSelect.add(newOptTemplate);

          // Atualiza todos os selects já renderizados na tabela
          document.querySelectorAll('.select-material-entry').forEach(sel => {
            const opt = new Option(mat.label, mat.id);
            opt.dataset.stock = mat.current_stock;
            opt.dataset.unit = mat.unit_measure;
            sel.add(opt);
            $(sel).trigger('change.select2');
          });

          // Seleciona o novo material na última linha ativa
          const allSelects = document.querySelectorAll('.select-material-entry');
          const lastSelect = allSelects[allSelects.length - 1];
          
          if (!lastSelect.value) {
            lastSelect.value = mat.id;
            $(lastSelect).trigger('change');
          } else {
            const newSelect = addEntryRow(mat.id, mat.current_stock, mat.unit_measure);
            $(newSelect).trigger('change');
          }

          bootstrap.Modal.getInstance(document.getElementById('modalQuickMaterial')).hide();
          form.reset();
          toastr.success(data.message);
        } else {
          toastr.error(data.message || 'Erro ao cadastrar material.');
        }
      })
      .catch(err => toastr.error('Falha na comunicação com o servidor.'));
    });

    document.getElementById('btnSubmitEntry').addEventListener('click', function() {
      const form = document.getElementById('formEntry');
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      confirmAction({
        title: 'Confirmar Entrada no Estoque?',
        text: 'Os saldos dos materiais selecionados serão incrementados imediatamente.',
        icon: 'question',
        confirmButtonText: 'Sim, Confirmar Entrada!',
        onConfirm: function() {
          form.submit();
        }
      });
    });
  });
</script>
@endpush
