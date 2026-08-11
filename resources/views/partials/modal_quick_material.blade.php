<div class="modal fade" id="modalQuickMaterial" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="formQuickMaterial" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-box-seam text-warning me-2"></i>Novo Material no Catálogo (Rápido)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Código SKU *</label>
            <input type="text" name="code_sku" class="form-control" placeholder="Ex: MAT-005" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nome do Material *</label>
            <input type="text" name="name" class="form-control" placeholder="Ex: Tinta Epóxi Cinza 18L" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Categoria *</label>
            <select name="category_id" class="form-select" required>
              <option value="">Selecione uma categoria...</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Unidade Medida *</label>
            <input type="text" name="unit_measure" class="form-control" placeholder="UN, KG, CX, M" value="UN" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">É Retornável?</label>
            <select name="is_returnable" class="form-select">
              <option value="0">Não (Consumo)</option>
              <option value="1">Sim (Ferramenta)</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Estoque Inicial (Saldo Atual) *</label>
            <input type="number" name="current_stock" class="form-control" value="0" min="0" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Estoque Mínimo *</label>
            <input type="number" name="minimum_stock" class="form-control" value="5" min="0" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nº CA (Exclusivo EPI)</label>
            <input type="text" name="ca_number" class="form-control" placeholder="Ex: CA 12345">
          </div>
          <div class="col-md-6">
            <label class="form-label">Validade CA (Exclusivo EPI)</label>
            <input type="date" name="ca_validity" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSaveQuickMaterial">Salvar e Selecionar</button>
      </div>
    </form>
  </div>
</div>
