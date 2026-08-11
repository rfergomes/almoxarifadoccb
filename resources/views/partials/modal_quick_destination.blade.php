<div class="modal fade" id="modalQuickDestination" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formQuickDestination" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Novo Destino (Rápido)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Código do Setor/Relatório *</label>
          <input type="text" name="code" class="form-control" placeholder="Ex: CO-005" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nome do Destino *</label>
          <input type="text" name="name" class="form-control" placeholder="Ex: C.O. Vila Real" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tipo de Destino *</label>
          <select name="type" class="form-select" required>
            <option value="casa_de_oracao">Casa de Oração</option>
            <option value="obra">Obra</option>
            <option value="administracao">Administração</option>
            <option value="outro">Outro</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Cidade</label>
          <input type="text" name="city" class="form-control" placeholder="Ex: São Paulo">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSaveQuickDestination">Salvar e Selecionar</button>
      </div>
    </form>
  </div>
</div>
