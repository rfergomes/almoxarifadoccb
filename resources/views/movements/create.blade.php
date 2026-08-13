@extends('layouts.app')

@section('title', 'Nova Saída | Almoxarifado CCB')
@section('page_title', 'Lançar Nova Saída / Empréstimo de Estoque')

@section('content')
<form action="{{ route('movements.store') }}" method="POST" id="formMovement">
  @csrf
  <div class="row g-3">
    <!-- Cabeçalho da Movimentação -->
    <div class="col-lg-12">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="bi bi-card-checklist text-primary me-2"></i>Dados da Movimentação</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tipo de Movimentação *</label>
              <select name="type" id="movementType" class="form-select" required>
                <option value="CONSUMPTION">Consumo Geral (Materiais Descartáveis)</option>
                <option value="EPI">Entrega de EPI (Apenas Categoria EPI)</option>
                <option value="LOAN">Empréstimo (Apenas Equipamentos/Retornáveis)</option>
              </select>
            </div>
            
            <div class="col-md-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-semibold mb-0">Beneficiário (Quem retira) *</label>
                @can('manage-beneficiaries')
                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-bold ms-auto" data-bs-toggle="modal" data-bs-target="#modalQuickBeneficiary">
                  <i class="bi bi-plus-circle"></i> Novo
                </button>
                @endcan
              </div>
              <select name="beneficiary_id" id="selectBeneficiary" class="form-select select2-enable" required>
                <option value="">Selecione o beneficiário...</option>
                @foreach($beneficiaries as $ben)
                  <option value="{{ $ben->id }}">{{ $ben->name }} ({{ $ben->role_in_ccb ?? 'Voluntário' }})</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-semibold mb-0">Destino de Aplicação *</label>
                @can('manage-destinations')
                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-bold ms-auto" data-bs-toggle="modal" data-bs-target="#modalQuickDestination">
                  <i class="bi bi-plus-circle"></i> Novo
                </button>
                @endcan
              </div>
              <select name="destination_id" id="selectDestination" class="form-select select2-enable" required>
                <option value="">Selecione a C.O. ou Obra...</option>
                @foreach($destinations as $dest)
                  <option value="{{ $dest->id }}">{{ $dest->code }} - {{ $dest->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Observações / Justificativa</label>
              <textarea name="notes" class="form-control" rows="2" placeholder="Ex: Material retirado para manutenção da iluminação da nave principal."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabela Dinâmica de Itens -->
    <div class="col-lg-12">
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="bi bi-box-seam text-warning me-2"></i>Itens da Movimentação</h5>
          <button type="button" class="btn btn-primary btn-sm rounded-pill ms-auto" id="btnAddItem">
            <i class="bi bi-plus-circle me-1"></i> Adicionar Outro Material
          </button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" id="tableItems">
              <thead class="table-light">
                <tr>
                  <th style="width: 40%;">Material *</th>
                  <th style="width: 15%;">Estoque Atual</th>
                  <th style="width: 15%;">Qtd. Retirada *</th>
                  <th style="width: 20%;" class="column-loan d-none">Data Prevista Retorno</th>
                  <th style="width: 10%;" class="text-center">Ações</th>
                </tr>
              </thead>
              <tbody id="itemsBody">
                <!-- Linhas inseridas via JS -->
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer bg-white border-0 py-3 d-flex flex-column flex-sm-row justify-content-end gap-2">
          <a href="{{ route('movements.index') }}" class="btn btn-light btn-touch">Cancelar</a>
          <button type="button" class="btn btn-success btn-touch px-4" id="btnSubmitMovement">
            <i class="bi bi-check-circle me-1"></i> Confirmar e Gravar Movimentação
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

@include('partials.modal_quick_beneficiary')
@include('partials.modal_quick_destination')

<!-- Template de Linha oculto -->
<template id="rowTemplate">
  <tr class="item-row">
    <td>
      <select name="items[INDEX][material_id]" class="form-select select-material select2-material" required>
        <option value="">Selecione o material...</option>
        @foreach($materials as $mat)
          <option value="{{ $mat->id }}" 
                  data-stock="{{ $mat->current_stock }}" 
                  data-unit="{{ $mat->unit_measure }}"
                  data-category="{{ $mat->category?->name }}"
                  data-is-epi="{{ $mat->isEpi() ? '1' : '0' }}"
                  data-returnable="{{ $mat->is_returnable ? '1' : '0' }}"
                  data-ca="{{ $mat->ca_number }}"
                  data-ca-validity="{{ $mat->ca_validity?->format('d/m/Y') }}"
                  data-ca-expired="{{ $mat->isCaExpired() ? '1' : '0' }}">
            {{ $mat->code_sku }} - {{ $mat->name }} ({{ $mat->current_stock }} {{ $mat->unit_measure }}) {{ $mat->is_returnable ? '[Retornável]' : '[Consumo]' }}
          </option>
        @endforeach
      </select>
      <small class="ca-info text-muted d-block mt-1"></small>
    </td>
    <td>
      <span class="stock-badge badge bg-secondary fs-6">-</span>
    </td>
    <td>
      <input type="number" name="items[INDEX][quantity]" class="form-control input-quantity" value="1" min="1" required>
    </td>
    <td class="column-loan d-none">
      <input type="date" name="items[INDEX][expected_return_date]" class="form-control input-date" min="{{ date('Y-m-d') }}">
      <small class="text-muted d-block mt-1 return-hint"></small>
    </td>
    <td class="text-center">
      <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row" title="Remover este item">
        <i class="bi bi-trash"></i>
      </button>
    </td>
  </tr>
</template>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const movementTypeSelect = document.getElementById('movementType');
    const itemsBody = document.getElementById('itemsBody');
    const btnAddItem = document.getElementById('btnAddItem');
    const rowTemplate = document.getElementById('rowTemplate').content;
    let rowIndex = 0;

    function applyMovementRules() {
      const type = movementTypeSelect.value;
      const isLoan = type === 'LOAN';
      const isConsumption = type === 'CONSUMPTION';
      const isEpi = type === 'EPI';

      // Mostra ou esconde cabeçalho de data de empréstimo/EPI
      document.querySelectorAll('.column-loan').forEach(el => {
        if (isLoan || isEpi) el.classList.remove('d-none');
        else el.classList.add('d-none');
      });

      // Aplica regras de filtragem nos selects de cada linha
      document.querySelectorAll('.item-row').forEach(row => {
        const select = row.querySelector('.select-material');
        const dateInput = row.querySelector('.input-date');
        const returnHint = row.querySelector('.return-hint');
        const selectedOpt = select.options[select.selectedIndex];

        Array.from(select.options).forEach(opt => {
          if (!opt.value) return;
          const isReturnable = opt.dataset.returnable === '1';
          const isEpiItem = opt.dataset.isEpi === '1';

          let allow = true;

          if (isLoan) {
            if (!isReturnable) allow = false;
          } else if (isConsumption) {
            if (isReturnable) allow = false;
          } else if (isEpi) {
            if (!isEpiItem) allow = false;
          }

          opt.disabled = !allow;

          if (!allow && opt.selected) {
            select.value = '';
            toastr.warning(`O material '${opt.text}' foi desmarcado pois não é permitido na modalidade selecionada.`);
          }
        });

        // Atualiza interface do Select2 para refletir opções desabilitadas
        $(select).trigger('change.select2');

        // Atualiza obrigatoriedade/dica do campo de data de retorno
        if (selectedOpt && selectedOpt.value) {
          const isReturnable = selectedOpt.dataset.returnable === '1';

          if (isLoan) {
            dateInput.required = true;
            dateInput.disabled = false;
            returnHint.textContent = 'Devolução obrigatória';
            returnHint.className = 'text-danger fw-bold small d-block mt-1';
          } else if (isEpi && isReturnable) {
            dateInput.required = false;
            dateInput.disabled = false;
            returnHint.textContent = 'EPI Retornável (Devolução opcional)';
            returnHint.className = 'text-primary small d-block mt-1';
          } else if (isEpi && !isReturnable) {
            dateInput.required = false;
            dateInput.disabled = true;
            dateInput.value = '';
            returnHint.textContent = 'EPI Descartável (Sem devolução)';
            returnHint.className = 'text-muted small d-block mt-1';
          } else {
            dateInput.required = false;
            dateInput.disabled = true;
            dateInput.value = '';
            returnHint.textContent = '';
          }
        }
      });
    }

    function addRow() {
      const clone = document.importNode(rowTemplate, true);
      const row = clone.querySelector('tr');
      row.innerHTML = row.innerHTML.replace(/INDEX/g, rowIndex++);
      itemsBody.appendChild(row);

      const selectMaterial = row.querySelector('.select-material');
      const stockBadge = row.querySelector('.stock-badge');
      const caInfo = row.querySelector('.ca-info');
      const inputQuantity = row.querySelector('.input-quantity');

      // Inicializa Select2 no novo select de material
      $(selectMaterial).select2({
        theme: 'bootstrap-5',
        placeholder: 'Selecione o material...',
        width: '100%'
      });

      $(selectMaterial).on('change', function() {
        const option = selectMaterial.options[selectMaterial.selectedIndex];
        if (option && option.value) {
          const stock = option.dataset.stock;
          const unit = option.dataset.unit;
          stockBadge.textContent = `${stock} ${unit}`;
          stockBadge.className = stock > 0 ? 'badge bg-success fs-6' : 'badge bg-danger fs-6';
          inputQuantity.max = stock;

          if (option.dataset.ca) {
            let caText = `CA: ${option.dataset.ca}`;
            if (option.dataset.caValidity) caText += ` | Validade: ${option.dataset.caValidity}`;
            if (option.dataset.caExpired === '1') caText += ` (VENCIDO!)`;
            caInfo.textContent = caText;
            caInfo.className = option.dataset.caExpired === '1' ? 'ca-info text-danger fw-bold d-block mt-1' : 'ca-info text-info d-block mt-1';
          } else {
            caInfo.textContent = '';
          }
        } else {
          stockBadge.textContent = '-';
          stockBadge.className = 'badge bg-secondary fs-6';
          caInfo.textContent = '';
        }

        applyMovementRules();
      });

      row.querySelector('.btn-remove-row').addEventListener('click', function() {
        if (itemsBody.querySelectorAll('tr').length > 1) {
          $(selectMaterial).select2('destroy');
          row.remove();
        } else {
          toastr.warning('A movimentação deve possuir ao menos um item!');
        }
      });

      applyMovementRules();
    }

    movementTypeSelect.addEventListener('change', applyMovementRules);
    btnAddItem.addEventListener('click', addRow);
    addRow();

    // AJAX Modal Beneficiário Rápido
    document.getElementById('btnSaveQuickBeneficiary').addEventListener('click', function() {
      const form = document.getElementById('formQuickBeneficiary');
      const formData = new FormData(form);

      fetch('/api/quick-beneficiary', {
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
          const select = document.getElementById('selectBeneficiary');
          const option = new Option(data.data.name, data.data.id, true, true);
          select.add(option);
          $(select).trigger('change');
          bootstrap.Modal.getInstance(document.getElementById('modalQuickBeneficiary')).hide();
          form.reset();
          toastr.success(data.message);
        } else {
          toastr.error(data.message || 'Erro ao cadastrar beneficiário.');
        }
      })
      .catch(err => toastr.error('Falha na comunicação com o servidor.'));
    });

    // AJAX Modal Destino Rápido
    document.getElementById('btnSaveQuickDestination').addEventListener('click', function() {
      const form = document.getElementById('formQuickDestination');
      const formData = new FormData(form);

      fetch('/api/quick-destination', {
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
          const select = document.getElementById('selectDestination');
          const option = new Option(data.data.name, data.data.id, true, true);
          select.add(option);
          $(select).trigger('change');
          bootstrap.Modal.getInstance(document.getElementById('modalQuickDestination')).hide();
          form.reset();
          toastr.success(data.message);
        } else {
          toastr.error(data.message || 'Erro ao cadastrar destino.');
        }
      })
      .catch(err => toastr.error('Falha na comunicação com o servidor.'));
    });

    // Confirm com SweetAlert2
    document.getElementById('btnSubmitMovement').addEventListener('click', function() {
      const form = document.getElementById('formMovement');
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      confirmAction({
        title: 'Confirmar Movimentação?',
        text: 'Deseja gravar a saída do estoque?',
        icon: 'question',
        confirmButtonText: 'Sim, Gravar Saída!',
        onConfirm: function() {
          form.submit();
        }
      });
    });
  });
</script>
@endpush
